<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('active')->default(1);
            $table->string('code',100);
            $table->integer('cat_id')->unsigned();
            $table->enum('warehouse_owner', ['provider', 'supplier']);
            $table->integer('owner_id')->unsigned();
            $table->integer('count');
            $table->integer('requested_count')->default(0);
            $table->double('price');
            $table->string('en_name');
            $table->string('ar_name');
            $table->string('en_desc');
            $table->string('ar_desc');
            $table->string('image', 500)->default('box.png');
            $table->timestamps();

            $table->foreign('cat_id')->references('id')->on('categories')->onUpdate('cascade') ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materials');
    }
}
