<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderRatesColsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $changeColsTypes = "
            ALTER TABLE `order_rates`
            CHANGE `appearance` `appearance` FLOAT(11) NOT NULL,
            CHANGE `cleanliness` `cleanliness` FLOAT(11) NOT NULL,
            CHANGE `performance` `performance` FLOAT(11) NOT NULL,
            CHANGE `commitment` `commitment` FLOAT(11) NOT NULL

        ";
        $addNewCol = "
            ALTER TABLE `order_rates`
            ADD `average` FLOAT AS (((((`appearance` + `cleanliness`) + `performance`) + `commitment`) / 4)) STORED
            AFTER `commitment`
        ";

        DB::statement($changeColsTypes);
        DB::statement($addNewCol);
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
