<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_in',
        'token_created_at'
    ];

    protected $casts = [
        'token_created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        if (!$this->token_created_at || !$this->expires_in) {
            return true;
        }

        return $this->token_created_at->addSeconds($this->expires_in - 300)->isPast();
    }
}