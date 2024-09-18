<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Announcement extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'user_id',
        'subject_id',
        'section_id',
        'full_text',
    ];

    public function scopeFilter($query, array $filters){
        $query->when($filters['search'] ?? false, fn ($query, $search) =>
            $query->where('announcements.title', 'like', '%'.$search.'%')
        );
    }

    public function getSlugOptions():SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
