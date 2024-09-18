<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGradeCat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // Add 'user_id' to the fillable attributes
        'section_id',
        'subject_id',
        'tabs',
        'category',
        'percentage',
        'user_id',
        'position',
    ];

    public function student_grade_sub(){
        return $this->hasMany(StudentGradeSub::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}
