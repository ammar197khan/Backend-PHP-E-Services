<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayOptionsToUserNotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_nots', function (Blueprint $table) {
            DB::statement("
                ALTER TABLE user_nots MODIFY COLUMN type ENUM('push', 'order', 'rate', 'time', 'notify', 'pay', 'confirm_payment');
            ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_nots', function (Blueprint $table) {
            //
        });
    }
}
