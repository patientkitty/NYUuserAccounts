<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalUDWTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_u_d_w_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('token_type')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('local_u_d_w_tokens');
    }
}
