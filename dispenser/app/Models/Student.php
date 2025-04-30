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
        'voucher_id',
        'email_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function email()
    {
        return $this->belongsTo(Email::class, 'email_id');
    }
    // Student.php
public function voucher()
{
    return $this->belongsTo(Voucher::class);
}

}

