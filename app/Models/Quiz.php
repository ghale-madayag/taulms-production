<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Quiz extends Model
{
    use HasFactory, HasSlug;
    const TYPE_TEXT = 'short answer';
    const TYPE_TEXTAREA = 'essay';
    const TYPE_RADIO = 'multiple choice';
    const TYPE_CHECKBOX = 'multiple answer';
    const TYPE_FILE_UPLOAD = 'file upload';
    
    protected $fillable = [
        'title',
        'slug',
        'user_id',
        'description',
        'excluded',
        'thumbnail',
        'student_grade_sub_id',
        'lesson_id',
        'lesson_tags',
        'position',
        'quiz_schedule',
        'end_date',
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
        $query->where('quizzes.title', 'like', '%'.$search.'%')
        )->when($filters['selected'] ?? false, function($query, $search){
            if($search==2){
                $search = 0;
            }
            $query->where('quizzes.published', 'like', '%'.$search.'%');
        });
    }

    public function scopeFilterAll( $query, array $filters){
            $query->when($filters['quiz'] ?? false, fn ($query, $search) =>
                $query->where('title', 'like', "%{$search}%")
            )->when($filters['status'] ?? false, function ($query, $search){
                if($search==2){
                    $search = 0;
                }
                $query->where('quizzes.published', 'like', '%'.$search.'%');
            })->when($filters['lesson'] ?? false, fn ($query, $search) =>
                $query->whereHas('lesson', function($query) use ($search){
                    $query->where('title', 'like', "%{$search}%");
                })
            )->when($filters['subject'] ?? false, fn ($query, $search) =>
                $query->whereHas('subject', function($query) use ($search){
                    $query->where('title', 'like', "%{$search}%");
                })
            )->when($filters['faculty'] ?? false, fn ($query, $faculty) =>
                $query->whereHas('user', function($query) use ($faculty){
                    $query->where('id', 'like', '%'.$faculty.'%')
                    ->orWhere('lname', 'like', "%{$faculty}%")
                    ->orWhere('fname', 'like','%'.$faculty.'%');
                })
            )->when($filters['section'] ?? false, fn ($query, $search) =>
                $query->whereHas('lesson.section', function($query) use ($search){
                    $query->where('name', 'like', "%{$search}%");
                })
            );
    }

    public function lesson(){
        return $this->belongsTo(Lesson::class);
    }

    public function question(){
        return $this->hasMany(Question::class);
    }
    

    public function student_quiz(){
        return $this->hasMany(StudentQuiz::class);
    }

    public function student_quiz_status(){
        return $this->hasMany(StudentQuizStatus::class)->where('finished',1);
    }

    public function student_quiz_stat(){
        return $this->hasMany(StudentQuizStatus::class);
    }

    public function student_quiz_status_pending(){
        $user = auth()->user()->id;
        return $this->hasMany(StudentQuizStatus::class)->where('finished',0)->where('user_id', $user);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
