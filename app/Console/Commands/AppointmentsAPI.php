<?php

namespace App\Console\Commands;

use App\Util\ApiAppointments;
use App\Util\AppointmentsHandler;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class AppointmentsAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command will fetch appointments from json & xml endpoints';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        $api = new ApiAppointments($client);
        $appointments = new AppointmentsHandler($api);
        $appointments->fetchAppointments();
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}
