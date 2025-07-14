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
    return $this->hasOne(Email::class, 'sch_id_number', 'school_id');
}
    // Student.php
public function voucher()
{
    return $this->belongsTo(Voucher::class);
}
public function satp()
{
    return $this->hasOne(SatpAccount::class, 'school_id', 'school_id');
}

public function schoology()
{
    return $this->hasOne(SchoologyCredential::class, 'school_id', 'school_id');
}

}

