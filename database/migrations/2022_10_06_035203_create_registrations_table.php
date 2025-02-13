<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('user_id')->nullable();
           // $table->foreign('user_id')->references('id')->on('users');
            $table->integer('term_id');
            //$table->foreign('term_id')->references('id')->on('terms');
            $table->timestamp('validation_date')->nullable();
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
        Schema::dropIfExists('registrations');
    }
};
