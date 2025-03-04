<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'student_id', 
        'accessed_at'
    ];

    public $timestamps = true;

    /**
     * Relationship: A transaction belongs to a student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
