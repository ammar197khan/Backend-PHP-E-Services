<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('order_id')->unsigned();
            $table->string('lat');
            $table->string('lng');
            $table->string('city')->nullable();
            $table->string('camp')->nullable();
            $table->string('street')->nullable();
            $table->string('plot_no')->nullable();
            $table->string('address')->nullable();
            $table->string('block_no')->nullable();
            $table->string('building_no')->nullable();
            $table->string('apartment_no')->nullable();
            $table->string('house_type')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_addresses');
    }
}
