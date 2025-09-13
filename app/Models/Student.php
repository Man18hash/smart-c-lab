<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'grade',
        'section',
        'address',
        'adviser',
        'phone_number',
        'email',
        'status', // e.g., 'active' | 'inactive'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
