<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Student extends Authenticatable
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'student_reg_number';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'student_reg_number',
        'student_first_name',
        'student_last_name',
        'student_gender',
        'student_email',
        'student_phone_number',
        'cdo_id'
    ];

    /**
     * Get the user account associated with this student.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'StudentRegNumber', 'student_reg_number');
    }
}