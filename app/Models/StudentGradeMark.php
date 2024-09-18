<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGradeMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'passing', 
        'cutoff',
        'lecture',
        'laboratory', 
        'section_id', 
        'subject_id', 
        'user_id', 
        'term_id',
    ];
}
