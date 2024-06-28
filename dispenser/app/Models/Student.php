<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $table = 'students';

    protected $fillable = [
        'school_id',
        'lastname',
        'firstname',
        'middlename',
        'course_id',
        'birthday',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}