<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ProductCategory Model
 * Purpose: Single level categories for physical supplies
 */
class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'parent_id',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // ========================================
    // BOOT METHOD FOR SLUG GENERATION
    // ========================================

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Str::slug($category->name);
            }
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Category has many products
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Category belongs to a parent category
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Category has many child categories
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Get active products in this category
     */
    public function getActiveProducts()
    {
        return $this->products()->active()->orderBy('sort_order')->orderBy('name')->get();
    }

    /**
     * Get product count in this category
     */
    public function getProductCount(): int
    {
        return $this->products()->count();
    }

    /**
     * Get active product count in this category
     */
    public function getActiveProductCount(): int
    {
        return $this->products()->active()->count();
    }

    /**
     * Check if category can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->getProductCount() === 0 && $this->children()->count() === 0;
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get category status badge for UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Get category status text for UI
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
} 