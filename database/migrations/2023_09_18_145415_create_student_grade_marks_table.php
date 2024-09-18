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
        Schema::create('student_grade_marks', function (Blueprint $table) {
            $table->id();
            $table->integer('passing');
            $table->integer('cutoff');
            $table->integer('lecture');
            $table->integer('laboratory');
            $table->integer('section_id');
            $table->foreign('section_id')->references('id')->on('sections');
            $table->integer('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->string('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('term_id');
            $table->foreign('term_id')->references('id')->on('terms');
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
        Schema::dropIfExists('student_grade_marks');
    }
};
