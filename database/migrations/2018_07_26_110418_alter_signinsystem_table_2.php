<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSigninsystemTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signinsystem',function (Blueprint $table){
            $table->string('userName')->nullable()->change();
            $table->string('account')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->string('signInTime')->nullable()->change();
            $table->string('signOutTime')->nullable()->change();
            $table->string('totalWorkTime')->nullable()->change();
            $table->string('token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
