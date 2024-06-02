<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyNotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE user_nots MODIFY COLUMN type ENUM
            (
              'push',
              'order',
              'rate',
              'time',
              'notify',
              'pay',
              'cash_not_recieved'
            );
        ");

        DB::statement("
            ALTER TABLE tech_nots MODIFY COLUMN type ENUM(
              'push',
              'order',
              'rate',
              'confirm_cash',
              'paid_online'
            );
        ");

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
