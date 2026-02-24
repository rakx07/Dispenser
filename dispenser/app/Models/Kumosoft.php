<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kumosoft extends Model
{
    protected $table = 'kumosofts';

    protected $fillable = [
        // keep old columns too (for backward compatibility)
        'school_id',
        'kumosoft_credentials',

        // new columns
        'student_id',
        'eis_school_id',
        'kumosoft_school_id',
        'lastname',
        'firstname',
        'middlename',
        'suffix',
        'email',
        'username',
        'password',
        'match_status',
        'match_reason',
        'matched_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}