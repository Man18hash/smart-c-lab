<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'status',   // 'free' | 'assigned' | 'blocked'
        'notes',
        'latitude',
        'longitude',
        'address',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function hasLocation(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}
