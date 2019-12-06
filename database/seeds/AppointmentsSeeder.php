<?php

use App\Util\ApiAppointments;
use App\Util\AppointmentsHandler;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;

class AppointmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();
        $api = new ApiAppointments($client);
        $appointments = new AppointmentsHandler($api);
        $appointments->fetchAppointments();
    }
}
