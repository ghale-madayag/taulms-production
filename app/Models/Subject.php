<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Subject extends Model
{
    use HasFactory, HasSlug;
    protected $fillable = [
        'thumbnail'
    ];

    public function student_grade_cat(){
        return $this->hasMany(StudentGradeCat::class);
    }

    public function scopeFilterBySubject($query, $search)
    {
        return $query->where('title', 'like', '%' . $search . '%');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function schedule()
    {
        return $this->hasMany(Schedule::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function scopeFilter($query, array $filters){

        $query->when($filters['search'] ?? false, fn ($query, $search) =>
        $query
            ->where('subjects.title', 'like', '%'.$search.'%')
            ->orWhere('subjects.code', 'like','%'.$search.'%'));
    }

    public function lesson(){
        return $this->hasMany(Lesson::class);
    }

    public function lessonTerm(){
        //$term = $this->student_courses()->select('term')->first();
        return $this->hasMany(Lesson::class);
    }

    public function ownSubject($user, $subject){
        $user_role = auth()->user()->roles->first()->id;
        $subjects = "";
        if($user_role == 1){
            $subjects = $this->select('subjects.id','subjects.title','subjects.description','subjects.slug','subjects.thumbnail','subjects.code','schedules.user_id','schedules.section_id','schedules.sched_date')
                ->join('schedules','subjects.id','schedules.subject_id')
                ->join('registration_details','schedules.id','registration_details.schedule_id')
                ->join('registrations','registration_details.registration_id','registrations.id')
                ->join('users','registrations.user_id','users.id')
                ->where('users.id',$user)
                ->where('subjects.id',$subject);
        }else{
            $subjects = $this->select('subjects.id','subjects.title','subjects.description','subjects.slug','subjects.thumbnail','subjects.code','schedules.section_id','schedules.sched_date')
                ->join('schedules','subjects.id','schedules.subject_id')
                ->where('schedules.user_id',$user);
        }
        return $subjects;
    }

    public function student_courses(){
        $user = auth()->user()->id;
        return $this->hasMany(StudentCourse::class)->where('user_id', $user);
    }

    public function student_lesson_statuses(){
        $user = auth()->user()->id;
        return $this->hasMany(StudentLessonStatus::class)
            ->where('user_id', $user)
            ->where('finished',1);
    }

    public function quizStat(){
        $lesson = $this->lesson()->with(['quiz' => function ($query) {
            $query->with('student_quiz_status');
        }]);
        return $lesson;
    }

    public function student_lesson_stats($term){
        $user = auth()->user()->id;
        return $this->hasMany(StudentLessonStatus::class)->where('finished',1)->where('term', $term)->where('user_id',$user);
    }

    public function student_lesson_statuses_pending(){
        return $this->hasMany(StudentLessonStatus::class)->where('finished',0)->with('lesson');
    }

    public function sreenrecord(){
        return $this->hasMany(ScreenRecord::class);
    }

    public function meet(){
        return $this->hasMany(Meet::class);
    }

    public function announcement(){
        return $this->hasMany(Announcement::class);
    }
}
