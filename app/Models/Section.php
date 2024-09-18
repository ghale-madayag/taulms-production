<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    public function schedule()
    {
        return $this->hasMany(Schedule::class);
    }

    public function subject()
    {
        return $this->hasMany(Subject::class);
    }

    public function lesson(){
        return $this->hasMany(Lesson::class);
    }

    public function screenrecord(){
        return $this->hasMany(ScreenRecord::class);
    }

    public function meet(){
        return $this->hasMany(Meet::class);
    }

    public function announcement(){
        return $this->hasMany(Announcement::class);
    }
}
