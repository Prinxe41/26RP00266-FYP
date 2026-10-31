<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Supervisor extends Authenticatable
{
    use HasFactory;

    protected $table = 'supervisors';
    protected $primaryKey = 'supervisor_email';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'supervisor_email',
        'supervisor_first_name',
        'supervisor_last_name',
        'supervisor_phone_number',
        'supervisor_password'
    ];

    protected $hidden = [
        'supervisor_password',
    ];

    // Map the password field to supervisor_password for authentication
    public function getAuthPassword()
    {
        return $this->supervisor_password;
    }
}