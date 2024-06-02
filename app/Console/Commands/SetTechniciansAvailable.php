<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class SetTechniciansAvailable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'technicians:free';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $busyTechnicians =
        DB::table('orders')
        ->where('completed', '!=', 1)
        ->whereNotNull('tech_id')
        ->distinct('tech_id')
        ->pluck('tech_id');

        $action =
        DB::table('technicians')
        ->whereNotIn('id', $busyTechnicians)
        ->where('busy', 1)
        ->update(['busy' => 0]);

        $this->info("($action)" . ' rows affected');
    }
}
