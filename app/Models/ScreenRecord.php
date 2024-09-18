<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenRecord extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'subject_id',
        'section_id',
        'video',
        'thumbnail',
        'start_at',
        'end_at',
    ];

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }
}
