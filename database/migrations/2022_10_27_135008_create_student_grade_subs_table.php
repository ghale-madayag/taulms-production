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
        Schema::create('student_grade_subs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('student_grade_cat_id');
            $table->integer('over');
            $table->foreign('student_grade_cat_id')->references('id')->on('student_grade_cats')->onDelete('cascade');
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
        Schema::dropIfExists('student_grade_subs');
    }
};
