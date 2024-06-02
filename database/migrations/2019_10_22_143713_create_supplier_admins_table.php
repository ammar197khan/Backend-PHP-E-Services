<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('badge_id')->nullable()->unique();
            $table->enum('administration', ['qreeb', 'provider', 'company', 'supplier']);
            $table->unsignedInteger('role_id')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->string('password');
            $table->string('image')->nullable()->default('default_admin.png');
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_admins');
    }
}
