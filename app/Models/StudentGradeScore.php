<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGradeScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'student_grade_sub_id',
        'term_id',
        'score',
        'over',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    public function student_grade_sub(){
        return $this->belongsTo(StudentGradeSub::class);
    }

}
