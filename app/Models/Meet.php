<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meet extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'subject_id',
        'section_id',
        'link',
        'meetID',
        'startDate',
        'endDate',
    ];

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }
}
