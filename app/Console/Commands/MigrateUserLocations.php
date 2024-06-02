<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MigrateUserLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:migrate-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate user locations from users table to user_locations table';

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
        DB::table('user_locations')
        ->select('companies.id')
        ->join('users', 'user_locations.user_id', '=', 'users.id')
        ->join('companies', 'users.company_id', '=', 'companies.id')
        ->where('company_id', '!=', 13)
        ->delete();

        Schema::table('user_locations', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
        });

        $users = DB::table('users')->where('company_id', '!=', 13)->get();

        foreach ($users as $user) {
            DB::table('user_locations')->insert([
                'name'                 => 'Main Address',
                'user_id'              => $user->id,
                'is_default'           => 1,
                'approved_by_employer' => 1,
                'lat'                  => $user->lat ?? 0,
                'lng'                  => $user->lng ?? 0,
                'address'              => null,
                'city'                 => $user->city,
                'camp'                 => $user->camp,
                'street'               => $user->street,
                'plot_no'              => $user->plot_no,
                'block_no'             => $user->block_no,
                'building_no'          => $user->building_no,
                'apartment_no'         => $user->apartment_no,
                'house_type'           => $user->house_type,
                'created_at'           => $user->created_at,
                'updated_at'           => $user->updated_at,
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'camp',
                'street',
                'plot_no',
                'block_no',
                'building_no',
                'apartment_no',
                'house_type',
                'lat',
                'lng'
            ]);
        });

    }
}
