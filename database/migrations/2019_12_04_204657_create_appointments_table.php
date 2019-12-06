<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status');
            $table->dateTime('start_at');
            $table->dateTime('booked_at');
            $table->bigInteger('patient_id')->unsigned();
            $table->bigInteger('doctor_id')->unsigned();
            $table->integer('clinic_id')->unsigned();
            $table->integer('speciality_id')->unsigned();

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients');

            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors');

            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics');

            $table->foreign('speciality_id')
                ->references('id')
                ->on('specialities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
