<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('active')->default(0);
            $table->string('ar_name')->unique();
            $table->string('en_name')->unique();
            $table->string('ar_desc')->unique();
            $table->string('en_desc')->unique();
            $table->string('email');
            $table->text('phones');
            $table->string('logo')->default('default_provider.png');
            $table->integer('address_id')->unsigned();
            $table->integer('parent_id')->unsigned();
            $table->timestamps();

            $table->foreign('address_id')->references('id')->on('addresses')->onUpdate('cascade') ->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('suppliers')->onUpdate('cascade') ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
