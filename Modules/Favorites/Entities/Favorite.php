<?php

namespace Modules\Favorites\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\User\Entities\User;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'favorable_type',
        'favorable_id',
        'favorite_type',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this favorite
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the favorited item (polymorphic)
     */
    public function favorable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by favorite type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('favorite_type', $type);
    }

    /**
     * Scope to filter by favorable type
     */
    public function scopeFavorableType($query, string $type)
    {
        return $query->where('favorable_type', $type);
    }

    /**
     * Check if a user has favorited an item
     */
    public static function isFavoritedByUser(int $userId, string $favorableType, int $favorableId, string $favoriteType = 'like'): bool
    {
        return static::where([
            'user_id' => $userId,
            'favorable_type' => $favorableType,
            'favorable_id' => $favorableId,
            'favorite_type' => $favoriteType,
        ])->exists();
    }

    /**
     * Get favorite count for an item
     */
    public static function getFavoriteCount(string $favorableType, int $favorableId, string $favoriteType = 'like'): int
    {
        return static::where([
            'favorable_type' => $favorableType,
            'favorable_id' => $favorableId,
            'favorite_type' => $favoriteType,
        ])->count();
    }
} 