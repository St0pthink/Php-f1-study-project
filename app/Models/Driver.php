<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'track_name',
        'short_description',
        'details_html',
        'image_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function setImagePathAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['image_path'] = null;
            return;
        }

        $value = trim($value);
        if (strpos($value, 'images/') === 0) {
            $this->attributes['image_path'] = $value;
            return;
        }

        $this->attributes['image_path'] = 'images/' . ltrim($value, '/');
    }
    public function getImageUrlAttribute(): string
    {
        $fallback = 'images/placeholder.png';
        $path = $this->image_path;
        if (!$path || !Storage::disk('public')->exists($path)) {
            return asset('storage/' . $fallback);
        }
        return asset('storage/' . $path);
    }

    protected static function booted()
    {
        // При создании автоматически привязываем текущего пользователя
        static::creating(function (Driver $driver) {
            if (Auth::check() && $driver->user_id === null) {
                $driver->user_id = Auth::id();
            }
        });

        // Дополнительная защита от удаления чужих карточек
        static::deleting(function (Driver $driver) {
            if (!Auth::check()) {
                return false;
            }

            $user = Auth::user();
            if (!$user->is_admin && $driver->user_id !== $user->id) {
                // Можно бросить исключение, но для лабы хватит просто запрета
                return false;
            }
        });

    
    }
}
