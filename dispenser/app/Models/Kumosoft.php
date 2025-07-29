<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kumosoft extends Model
{
    protected $fillable = [
        'school_id',
        'kumosoft_credentials',
    ];
}