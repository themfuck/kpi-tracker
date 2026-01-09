<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiTarget extends Model
{
    protected $fillable = [
        'gmv_per_hour',
        'conversion_rate',
        'aov',
        'likes_per_minute',
    ];

    protected $casts = [
        'gmv_per_hour' => 'decimal:2',
        'conversion_rate' => 'decimal:4',
        'aov' => 'decimal:2',
    ];
}
