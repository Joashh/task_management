<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'order',
        'user_id',
    ];

    // Relationship: A task belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
