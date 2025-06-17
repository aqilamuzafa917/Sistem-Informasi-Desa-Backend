<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevokeLog extends Model
{
    protected $fillable = [
        'revoker_id',
        'revoked_user_id',
        'revoked_at'
    ];

    protected $casts = [
        'revoked_at' => 'datetime'
    ];

    public function revoker()
    {
        return $this->belongsTo(User::class, 'revoker_id');
    }

    public function revokedUser()
    {
        return $this->belongsTo(User::class, 'revoked_user_id');
    }
} 