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
        Schema::create('scheduling_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('command'); 
            $table->enum('interval', ['daily', 'weekly', 'monthly', 'yearly']); 
            $table->string('schedule_time'); 
            $table->string('days_of_week')->nullable(); 
            $table->integer('day_of_month')->nullable();
            $table->string('month')->nullable();
            
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
        Schema::dropIfExists('scheduling_configuration');
    }
};
