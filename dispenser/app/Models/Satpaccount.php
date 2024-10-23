<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satpaccount extends Model
{
    use HasFactory;

    protected $table ='satpaccounts';

    protected $fillable = [
        'student_id',
        'satp_password',
    ];

}
