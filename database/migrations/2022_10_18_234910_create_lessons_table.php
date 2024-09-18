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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->integer('section_id');
            $table->foreign('section_id')->references('id')->on('sections');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('short_text')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('full_text')->nullable();
            $table->tinyInteger('term');
            $table->integer('position')->nullable()->default(0);
            $table->tinyInteger('published')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lessons');
    }
};
