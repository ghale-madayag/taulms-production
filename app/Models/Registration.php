<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $casts = [
        'validation_date' => 'datetime:Y-m-d',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function registration_details()
    {
        return $this->hasMany(RegistrationDetails::class);
    }
}
