<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmsRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ems_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('room')->nullable();
            $table->string('description')->nullable();
            $table->string('room_id')->nullable();
            $table->string('building')->nullable();
            $table->string('building_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ems_rooms');
    }
}
