<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->json('commission_categories')->after('interest_fee')->nullable();
            DB::statement("ALTER TABLE providers MODIFY COLUMN type ENUM('percentage','cash','categorized')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
          $table->dropColumn('commission_categories');
          DB::statement("ALTER TABLE providers MODIFY COLUMN type ENUM('percentage','cash')");
        });
    }
}
