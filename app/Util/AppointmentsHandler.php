<?php
namespace App\Util;

use App\Appointment;
use App\Clinic;
use App\Doctor;
use App\Patient;
use App\Speciality;
use Carbon\Carbon;

class AppointmentsHandler {
    protected $api;
    protected $jsonData;
    protected $from;
    protected $to;
    public function __construct(ApiAppointments $appointments) {
        $this->api = $appointments;
        $this->jsonData = [];
        $this->from = Carbon::today()->addDays(1)->format('Y-m-d 00:00:00');
        $this->to = Carbon::now()->addDays(30)->format('Y-m-d 23:59:59');
    }

    public function fetchAppointments() {
        $token = $this->api->apiAuth();

        if($token) {
            $resultJSON = $this->api->endpointRequestJSON('/json?from='.$this->from);
            if(count($resultJSON->data) > 0) {

                $this->addAppointment($resultJSON->data, 'json');
                for($i = 2; $i <= $resultJSON->last_page; $i++) { //
                    $resultJSON = $this->api->endpointRequestJSON('/json?from='.$this->from.'&page='.$i);
                    if(count($resultJSON->data) > 0) {
                        $anotherApiCall = $this->addAppointment($resultJSON->data, 'json');
                        if(!$anotherApiCall) {
                            break;
                        }
                    } else {
                        break;
                    }

                }
            }

        }
        for($day = 0; $day<30; $day++) {
            $from_date = new Carbon($this->from);
            $next_date = $from_date->addDays($day)->format('Y-m-d H:i:s');
            $resultXML = $this->api->endpointRequestXML('/xml?from=' . $next_date);
            $xmlToJson = json_encode($resultXML);
            $data = json_decode($xmlToJson);
            $appointments = $data->appointment;
            if (count($appointments)) {
                $anotherApiCall = $this->addAppointment($appointments, 'xml');
                if(!$anotherApiCall) {
                    break;
                }
            }
        }
    }

    protected function addAppointment($appointments, $type) {
        foreach($appointments as $appointment) {

            $appointment_date = $type === 'json' ? $appointment->datetime
                :
                Carbon::createFromFormat('m/d/Y h:i:s a', $appointment->start_date.' '. $appointment->start_time)->toDateTimeString();
            $patient_dob  = $type === 'json' ? new Carbon($appointment->patient->dob) : new Carbon($appointment->patient->date_of_birth);

            if($appointment_date >= $this->to) { // appointment is not with in month so return and no more calls.
                return false;
            }

            $patient_age_years = (int) Carbon::createFromDate($patient_dob->year, $patient_dob->month, $patient_dob->day)
                ->diff(Carbon::now())->format('%y');
            $patient_age_month = (int) Carbon::createFromDate($patient_dob->year, $patient_dob->month, $patient_dob->day)
                ->diff(Carbon::now())->format('%m');
            if((($patient_age_years * 12) + $patient_age_month) > 216) { //if patient is above 18 years do nothing
                continue;
            }

            $speciality = Speciality::where('id', $appointment->specialty->id)->first();
            if(!$speciality) { // Add speciality if doesn't exists
                $speciality = Speciality::create([
                    'id' => $appointment->specialty->id,
                    'name' => $appointment->specialty->name
                ]);
            }

            $clinic = Clinic::where('id', $appointment->clinic->id)->first();
            if(!$clinic) { // Add clinic if doesn't exists
                $clinic = Clinic::create([
                    'id' => $appointment->clinic->id,
                    'name' => $appointment->clinic->name
                ]);
            }

            $doctor = Doctor::where('id', $appointment->doctor->id)->first();
            if(!$doctor) {
                $doctor = Doctor::create([
                    'id' => $appointment->doctor->id,
                    'name' => $appointment->doctor->name
                ]);
            }

            $patient = Patient::where('id', $appointment->patient->id)->first();
            $gender = $type === 'json' ? $appointment->patient->gender : ($appointment->patient->sex === '1' ? 'male': 'female');
            if(!$patient) {
                $patient = Patient::create([
                    'id' => $appointment->patient->id,
                    'name' => $appointment->patient->name,
                    'gender' => $gender,
                    'dob' => $patient_dob->toDateString()
                ]);
            }

            $my_appointment = Appointment::where('id', $appointment->id)->first();
            $booked_at = $type === 'json' ? $appointment->created_at : $appointment->booked_at;
            $status = ($type === 'json') ? $appointment->status : ($appointment->cancelled == '1' ? 'cancelled' : 'booked');

            if(!$my_appointment && $status === 'booked') {
                Appointment::create([
                    'id' => $appointment->id,
                    'status' => $status,
                    'start_at' => $appointment_date,
                    'booked_at' => $booked_at,
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'speciality_id' => $speciality->id,
                    'clinic_id' => $clinic->id
                ]);
            } else if($my_appointment && ($my_appointment->status !== $status ||
                    $my_appointment->start_at != $appointment_date)) {
                $my_appointment->status = $status;
                $my_appointment->start_at = $appointment_date;

                $my_appointment->save();
            }

        }
        return true;
    }
}
