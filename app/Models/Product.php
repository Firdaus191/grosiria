<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'slug',
        'description',
        'image_path',
        'base_price',
        'discount_price',
        'stock',
        'unit',
        'bonus_points',
        'sold_count',
        'is_active'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
        'bonus_points' => 'integer',
        'sold_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scope untuk Static Recommendation Engine (ORDER BY sold_count DESC)
    public function scopeRecommended(Builder $query, int $limit = 10): Builder
    {
        return $query->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderByDesc('sold_count')
            ->limit($limit);
    }
}
