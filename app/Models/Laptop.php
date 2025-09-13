<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_name',
        'image_path',
        'status',
        'notes',
    ];

    /**
     * Public URL via the /storage symlink, or fallback image.
     */
    public function imageUrl(): string
    {
        if ($this->image_path) {
            return asset('storage/' . ltrim($this->image_path, '/'));
        }
        return asset('images/no-image.png'); // ensure this file exists at public/images/no-image.png
    }
}
