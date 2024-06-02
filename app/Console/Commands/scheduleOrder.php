<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class scheduleOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily assign technicians to scheduled orders';

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
        //
	$client = new \GuzzleHttp\Client();
        $request = $client->get('http://15.184.73.9/crone/schedule');
        $response = $request->getBody()->getContents();
        $schedule = json_decode($response, true);
    }
}
