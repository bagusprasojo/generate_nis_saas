<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NisPattern extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'pattern',
        'reset_rule',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
