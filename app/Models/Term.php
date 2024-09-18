<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = ['id','academic_year', 'term','start_ay', 'end_ay'];

    public function registration()
    {
        return $this->hasMany(Registration::class);
    }
}
