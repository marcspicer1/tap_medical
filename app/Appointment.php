<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'status', 'start_at', 'booked_at', 'patient_id', 'doctor_id', 'clinic_id', 'speciality_id'];

    public function patient() {
        return $this->belongsTo('App\Patient');
    }

    public function doctor() {
        return $this->belongsTo('App\Doctor');
    }

    public function clinic() {
        return $this->belongsTo('App\Clinic');
    }

    public function speciality() {
        return $this->belongsTo('App\Speciality');
    }
}
