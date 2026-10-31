<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campus extends Model
{
    use HasFactory;

    protected $primaryKey = 'campus_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['campus_name'];
}