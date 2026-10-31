<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Hod extends Authenticatable
{
    use HasFactory;

    protected $table = 'hods';

    protected $fillable = [
        'first_name',
        'last_name',
        'hod_email',
        'hod_phone',
        'campus_id',
        'department_code',
        'password'
    ];

    protected $hidden = [
        'password',
    ];
}