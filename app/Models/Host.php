<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    protected $fillable = [
        'name',
        'role',
        'is_active',
        'photo_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function liveSessions()
    {
        return $this->hasMany(LiveSession::class);
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }
}
