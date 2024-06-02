<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCategoriesDefaultPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            DB::statement('ALTER TABLE `categories` CHANGE COLUMN `urgent_price` `urgent_price` FLOAT DEFAULT 0;');
            DB::statement('ALTER TABLE `categories` CHANGE COLUMN `scheduled_price` `scheduled_price` FLOAT DEFAULT 0;');
            DB::statement('ALTER TABLE `categories` CHANGE COLUMN `third_price` `third_price` FLOAT DEFAULT 0;');
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
