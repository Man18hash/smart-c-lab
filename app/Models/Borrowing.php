<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'laptop_id',
        'ip_asset_id',
        'status',
        'purpose',
        'duration_hours',
        'requested_at',
        'approved_at',
        'checked_out_at',
        'due_at',
        'returned_at',
        'approved_by_admin_id',
        'checked_out_by_admin_id',
        'checked_in_by_admin_id',
        'student_snapshot_json',
        'device_snapshot_json',
        'ip_snapshot_json',
        'remarks',
    ];

    protected $casts = [
        'requested_at'     => 'datetime',
        'approved_at'      => 'datetime',
        'checked_out_at'   => 'datetime',
        'due_at'           => 'datetime',
        'returned_at'      => 'datetime',
        'student_snapshot_json' => 'array',
        'device_snapshot_json'  => 'array',
        'ip_snapshot_json'      => 'array',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class);
    }

    public function laptop()
    {
        return $this->belongsTo(\App\Models\Laptop::class);
    }

    public function ipAsset()
    {
        return $this->belongsTo(\App\Models\IpAsset::class, 'ip_asset_id');
    }

    // Helpers
    public function getIsRunningAttribute(): bool
    {
        return in_array($this->status, ['approved', 'checked_out'], true) && !empty($this->due_at);
    }

    public function scopeSearch($q, string $term = '')
    {
        if ($term === '') return $q;

        return $q->where(function ($qq) use ($term) {
            $qq->whereHas('student', function ($s) use ($term) {
                    $s->where('full_name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
                })
               ->orWhereHas('laptop', function ($l) use ($term) {
                    $l->where('device_name', 'like', "%{$term}%");
                });
        });
    }
}
