<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $table = 'course';
        
    protected $fillable = [
        'code',
        'name',
        'department_id',
        'status',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'course_id');
    }
}
