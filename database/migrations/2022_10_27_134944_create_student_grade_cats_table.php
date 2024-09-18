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
        Schema::create('student_grade_cats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('section_id');
            $table->foreign('section_id')->references('id')->on('sections');
            $table->integer('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->string('tabs');
            $table->string('category');
            $table->integer('percentage');
            $table->string('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('position')->nullable();
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
        Schema::dropIfExists('student_grade_cats');
    }
};
