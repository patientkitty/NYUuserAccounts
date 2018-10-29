<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEMSuserUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_m_suser_uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('userName')->nullable();
            $table->string('NetID')->nullable();
            $table->string('userType')->nullable();
            $table->integer('importGroupID')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('e_m_suser_uploads');
    }
}
