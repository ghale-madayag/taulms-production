<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Lesson extends Model
{
    use HasFactory, HasSlug;
    protected $fillable = [
        'title',
        'slug',
        'user_id',
        'short_text',
        'thumbnail',
        'subject_id',
        'section_id',
        'full_text',
        'term',
        'position',
        'published'
    ];

    public function getSlugOptions():SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function scopeFilter($query, array $filters){

        $query->when($filters['search'] ?? false, fn ($query, $search) =>
            $query->where('lessons.title', 'like', '%'.$search.'%')
        )->when($filters['selected'] ?? false, function($query, $search){
            if($search==2){
                $search = 0;
            }
            $query->where('lessons.published', 'like', '%'.$search.'%');
        });
    }

    public function scopeFilterAll( $query, array $filters){
        $query->when($filters['lesson'] ?? false, fn ($query, $search) =>
                $query->where('title', 'like', "%{$search}%")
            
            )->when($filters['status'] ?? false, function ($query, $search){
                if($search==2){
                    $search = 0;
                }
                $query->where('lessons.published', 'like', '%'.$search.'%');
            })->when($filters['subject'] ?? false, fn ($query, $search) =>
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

    public function attachement(){
        return $this->hasMany(LessonAttachement::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function quiz(){
        return $this->hasMany(Quiz::class);
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
