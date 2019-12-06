<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request) {
        $perPage = 50;
        $appointments = Appointment::with(['doctor', 'clinic', 'patient', 'speciality'])->where('status', 'booked');
        if($request->doctor && $request->start_date) {
            $appointments = $appointments->whereHas('doctor', function ($query) use($request) {
                $query->where('id', $request->doctor);
            })
                ->whereDate('start_at', $request->start_date);
        } else {
            $appointments = $appointments->where('start_at', '>', Carbon::now());
        }
        $appointments = $appointments->orderBy('start_at')
            ->paginate($perPage);
        $doctors = Doctor::orderBy('name')->get();
        return view('appointments', [
            'appointments' => $appointments,
            'doctors' => $doctors,
            'filter_doctor' => $request->doctor,
            'filter_date' => $request->start_date
        ]);
    }
}
