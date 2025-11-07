<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Your DB uses `course` (singular), not the Laravel default `courses`
    protected $table = 'course';

    protected $primaryKey = 'id';
    public $timestamps = false; // set true if you actually have created_at/updated_at

    protected $fillable = [
        'code', 'name',
    ];
}
