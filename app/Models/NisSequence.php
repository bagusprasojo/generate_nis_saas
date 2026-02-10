<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NisSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'reset_key',
        'last_sequence',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
