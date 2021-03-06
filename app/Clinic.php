<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    public $timestamps = false;

    protected $fillable = ['id', 'name'];

    public function appointments() {
        return $this->hasMany('App\Appointment');
    }
}
