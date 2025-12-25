<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'user_id',
        'body',
    ];

    /**
     * Комментарий принадлежит карточке (Driver)
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Комментарий принадлежит пользователю (автор)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
