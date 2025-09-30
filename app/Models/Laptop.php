<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

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
            // Use the public disk so it respects configured filesystems
            /** @var FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            $path = ltrim($this->image_path, '/');
            
            if ($disk->exists($path)) {
                // Generate URL with current request host and port
                $scheme = request()->getScheme();
                $host = request()->getHost();
                $port = request()->getPort();
                
                // Build the correct URL
                $url = $scheme . '://' . $host;
                if ($port !== 80 && $port !== 443) {
                    $url .= ':' . $port;
                }
                $url .= '/storage/' . $path;
                
                return $url;
            }
        }
        // Fallback placeholder
        return asset('images/no-image.svg');
    }

    /**
     * Delete the associated image file when the laptop is deleted.
     */
    protected static function booted()
    {
        static::deleting(function ($laptop) {
            if ($laptop->image_path) {
                Storage::disk('public')->delete($laptop->image_path);
            }
        });
    }
}
