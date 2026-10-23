<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'category_name',
        'brand',
        'scale',
        'item_condition',
        'price',
        'stock',
        'rating',
        'img',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'rating' => 'float',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getImgUrlAttribute(): string
    {
        if (! $this->img) {
            return 'https://placehold.co/100x100?text=No+Image';
        }

        if (Str::startsWith($this->img, ['http://', 'https://'])) {
            return $this->img;
        }

        return asset('storage/'.ltrim($this->img, '/'));
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('product_id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
