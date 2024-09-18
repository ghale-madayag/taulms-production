<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGradeSub extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', // Add 'user_id' to the fillable attributes
        'student_grade_cat_id',
        'over',
    ];

    public function student_grade_cat(){
        return $this->belongsTo(StudentGradeCat::class);
    }

    public function student_grade_score(){
        return $this->hasMany(StudentGradeScore::class);
    }
}
