<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'address',
    ];

    public function pattern()
    {
        return $this->hasOne(NisPattern::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function sequences()
    {
        return $this->hasMany(NisSequence::class);
    }
}
