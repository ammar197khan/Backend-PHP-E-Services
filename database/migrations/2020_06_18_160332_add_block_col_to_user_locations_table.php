<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBlockColToUserLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_locations', function (Blueprint $table) {
            $table->boolean('is_default')->default(0)->comment('Default address 0 false, 1 true')->before('created_at');
            $table->boolean('approved_by_employer')->default(0)->comment('btb approve by admin 0 false can edit by user
             1 true cannot edit by user')->before('created_at');
            $table->string('city')->nullable()->before('created_at');
            $table->string('camp')->nullable()->before('created_at');
            $table->string('street')->nullable()->before('created_at');
            $table->string('plot_no')->nullable()->before('created_at');
            $table->string('block_no')->nullable()->before('created_at');
            $table->string('building_no')->nullable()->before('created_at');
            $table->string('apartment_no')->nullable()->before('created_at');
            $table->string('house_type')->nullable()->before('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_locations', function (Blueprint $table) {
            //
        });
    }
}
