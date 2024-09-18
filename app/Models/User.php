<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'email',
        'password',
        'fname',
        'mname',
        'lname',
        'initial',
        'extname',
        'term_id',
        'date_admmitted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeFilterStudent($query, array $filters){
        $query->when($filters['search'] ?? false, fn ($query, $search) =>
            $query->where('id', 'like', '%'.$search.'%')
            ->orWhere('lname', 'like', "%{$search}%")
            ->orWhere('fname', 'like','%'.$search.'%')
        );
    }

    public function scopeFilter($query, array $filters){

        $query->when($filters['search'] ?? false, fn ($query, $search) =>
                $query
                    ->where('users.id', 'like', '%'.$search.'%')
                    ->orWhere('users.fname', 'like','%'.$search.'%')
                    ->orWhere('users.lname', 'like','%'.$search.'%')
                )->when($filters['roles'] ?? false, function($query, $roles) {
                    $query->whereHas('roles', function($query) use ($roles){
                        $query->where('name', 'like', "%{$roles}%");
                    });
                });

        $query->when($filters['selected'] ?? false, function($query, $selected) {
            if ($selected=== 'validated') {
                $query->whereNotNull('validation_date');
            } elseif ($selected == 'notvalidated') {
                $query->whereNull('validation_date');
            }
        });

    }

    public function registration(){
        return $this->hasMany(Registration::class);
    }

    public function schedule(){
        return $this->hasMany(Schedule::class);
    }

    public function student_lesson_statuses(){
        return $this->hasMany(StudentLessonStatus::class);
    }

    public function student_quizzes(){
        return $this->hasMany(StudentQuiz::class);
    }

    public function announcement(){
        return $this->hasMany(Announcement::class);
    }

    public function student_grade_scores(){
        return $this->hasMany(StudentGradeScore::class, 'user_id', 'id');
    }

}
