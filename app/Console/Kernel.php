<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        echo $url = url('/').'/crone/schedule';
        $schedule->command('db:backup')->everyMinute();
	    $schedule->call(function () {
            $ch = curl_init();
            $env_url = "https://admin.qreebs.com/crone/schedule";
            $env = env('APP_ENV');
            if($env == 'local'){
                $env_url = "http://127.0.0.1:8000";
            }elseif($env == 'staging'){
                $env_url = "http://15.184.73.9/crone/schedule";
            }elseif($env == 'production'){
                $env_url = "https://admin.qreebs.com/crone/schedule";
            }elseif($env == 'develop'){
                $env_url = "http://15.184.73.9/crone/schedule";
            }  
            curl_setopt($ch, CURLOPT_URL, $env_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
