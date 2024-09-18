<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['student_quiz_id','question_id','answer','points'];

    public function student_quiz(){
        return $this->belongsTo(StudentQuiz::class);
    }

    public function question(){
        return $this->belongsTo(Question::class);
    }
}
