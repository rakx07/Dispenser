<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentUser extends Model
{
    use HasFactory;

    protected $table = 'studentusers';

    protected $fillable = [
        'school_id',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];
}
