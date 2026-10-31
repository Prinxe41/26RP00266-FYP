<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Project;


class Message extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'message_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_email',
        'receiver_email',
        'project_code',
        'message_body',
        'timestamp'
    ];

    /**
     * Get the project that the message belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_code', 'project_code');
    }

    /**
     * Get the sender user.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_email', 'email');
    }

    /**
     * Get the receiver user.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_email', 'email');
    }
}