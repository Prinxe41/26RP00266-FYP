<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    //
    protected $table = 'faculties'; // explicitly set table name (optional if plural)

    protected $primaryKey = 'faculty_code'; // if you're using faculty_code as PK

    public $incrementing = false; // if faculty_code is not auto-increment

    protected $keyType = 'string'; // if faculty_code is a string

    protected $fillable = [
        'faculty_code',
        'faculty_name',
        'department_code',
    ];
}