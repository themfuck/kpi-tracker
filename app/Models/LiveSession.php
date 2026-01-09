<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    protected $fillable = [
        'host_id',
        'date',
        'hours_live',
        'gmv',
        'orders',
        'viewers',
        'likes',
        'errors',
    ];

    protected $casts = [
        'date' => 'date',
        'hours_live' => 'float',
        'gmv' => 'decimal:2',
    ];

    public function host()
    {
        return $this->belongsTo(Host::class);
    }
}
