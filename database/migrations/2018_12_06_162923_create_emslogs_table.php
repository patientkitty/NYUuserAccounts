<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmslogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emslogs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('userName')->nullable();
            $table->string('NetID')->nullable();
            $table->string('userType')->nullable();
            $table->string('eventRequester')->nullable();
            $table->string('webAppUser')->nullable();
            $table->string('uploadedBy')->nullable();
            $table->string('uploadedFile')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emslogs');
    }
}
