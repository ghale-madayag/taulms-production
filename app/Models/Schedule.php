<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    public function scopeFilter($query, array $filters){
            $query->when($filters['subject'] ?? false, fn ($query, $search) =>
                $query->whereHas('subject', function($query) use ($search){
                    $query->where('title', 'like', "%{$search}%");
                })
            )->when($filters['faculty'] ?? false, fn ($query, $faculty) =>
                $query->whereHas('user', function($query) use ($faculty){
                    $query->where('id', 'like', '%'.$faculty.'%')
                    ->orWhere('lname', 'like', "%{$faculty}%")
                    ->orWhere('fname', 'like','%'.$faculty.'%');
                })
            )->when($filters['section'] ?? false, fn ($query, $section) =>
                $query->whereHas('section', function($query) use ($section){
                    $query->where('name', 'like', "%{$section}%");
                })
            );
    }

    public function registration_details()
    {
        return $this->hasMany(RegistrationDetails::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
