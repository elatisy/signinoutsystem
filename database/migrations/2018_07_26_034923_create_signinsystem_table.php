<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSigninsystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signinsystem', function (Blueprint $table) {
            $table->increments('id');
            $table->string('userName');
            $table->string('account');
            $table->string('password');
            $table->string('phoneNumber');
            $table->string('signInTime');
            $table->string('signOutTime');
            $table->string('totalWorkTime');
            $table->string('token');
            $table->string('authCode');
            $table->string('authCode_requestTime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('signinsystem');
    }
}
