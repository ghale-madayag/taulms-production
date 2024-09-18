<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonAttachement extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'lesson_id',
        'attachement',
    ];

    public function lesson(){
        return $this->belongsTo(Lesson::class);
    }
}
