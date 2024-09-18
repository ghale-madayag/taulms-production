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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('lesson_tags')->nullable();
            $table->string('title', 1000);
            $table->string('slug', 1000);
            $table->text('description')->nullable();
            $table->text('excluded')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('student_grade_sub_id');
            $table->foreign('student_grade_sub_id')->references('id')->on('student_grade_subs')->onDelete('cascade');
            $table->tinyInteger('published')->nullable()->default(0);
            $table->integer('position')->nullable()->default(0);
            $table->timestamp('quiz_schedule')->nullable();
            $table->timestamp('end_date')->nullable();
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
        Schema::dropIfExists('quizzes');
    }
};
