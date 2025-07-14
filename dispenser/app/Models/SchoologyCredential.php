<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoologyCredential extends Model
{
    protected $fillable = [
        'school_id',
        'schoology_credentials',
    ];
}
