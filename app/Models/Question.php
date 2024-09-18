<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = ['type','question','description','data','answer','points','quiz_id'];

    public function quiz() {
        return $this->belongsTo(Quiz::class);
    }

    public function student_quiz_status(){
        return $this->hasMany(StudentQuizStatus::class);
    }

    public function answer(){
        return $this->hasMany(Answer::class);
    }
}
