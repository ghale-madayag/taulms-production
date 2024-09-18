<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulingConfiguration extends Model
{
    protected $table = 'scheduling_configurations';
    protected $fillable = [
        'name', 'command', 'interval', 'schedule_time', 'days_of_week', 'day_of_month', 'month'
    ];
}
