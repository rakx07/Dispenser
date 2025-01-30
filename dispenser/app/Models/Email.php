<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'emails'; // Ensure table name matches

    protected $fillable = [
        'first_name',
        'last_name',
        'email_address',
        'password',
        'sch_id_number',
    ];
}