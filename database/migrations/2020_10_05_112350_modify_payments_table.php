<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            DB::statement("
                ALTER TABLE `payments` CHANGE `transaction_id` `transaction_id` VARCHAR(11) NULL;
            ");
            DB::statement("
                ALTER TABLE `payments` CHANGE `payment_type` `payment_type` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'cash, online';
            ");
            DB::statement("
                ALTER TABLE `payments` CHANGE `paid_at` `paid_at` TIMESTAMP NULL;
            ");
            DB::statement("
                ALTER TABLE `payments` CHANGE `online_payment_type` `online_payment_type` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;
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
        //
    }
}
