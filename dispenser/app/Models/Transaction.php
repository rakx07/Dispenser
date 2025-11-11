<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = ['student_id', 'accessed_at'];

    // Nice-to-have: lets you format in Blade easily
    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public $timestamps = true;

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
