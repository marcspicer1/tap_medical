<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['id', 'name', 'gender', 'dob'];

    public function appointments() {
        return $this->hasMany('App\Appointment');
    }
}
