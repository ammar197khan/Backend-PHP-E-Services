<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePriceNullValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('categories', function (Blueprint $table) {
          DB::table('categories')->whereNull('urgent_price')->update(['urgent_price' => 0]);
          DB::table('categories')->whereNull('scheduled_price')->update(['scheduled_price' => 0]);
          DB::table('categories')->whereNull('third_price')->update(['third_price' => 0]);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
