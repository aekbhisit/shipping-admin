<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Product Model
 * Purpose: Physical supplies (boxes, tape, packaging materials)
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'sku',
        'image_path',
        'price',
        'unit',
        'dimensions',
        'weight',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:3',
        'dimensions' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Product belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Product has many branch products (availability)
     */
    public function branchProducts()
    {
        return $this->hasMany(BranchProduct::class);
    }

    /**
     * Product belongs to many branches through branch_products
     */
    public function branches()
    {
        return $this->belongsToMany(\Modules\Branch\Entities\Branch::class, 'branch_products')
                    ->withPivot('is_available', 'branch_price')
                    ->withTimestamps();
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Check if product is available in specific branch
     */
    public function isAvailableInBranch(int $branchId): bool
    {
        $branchProduct = $this->branchProducts()
            ->where('branch_id', $branchId)
            ->first();

        return $branchProduct ? $branchProduct->is_available : false;
    }

    /**
     * Get branch-specific price or global price
     */
    public function getBranchPrice(int $branchId): float
    {
        $branchProduct = $this->branchProducts()
            ->where('branch_id', $branchId)
            ->first();

        if ($branchProduct && $branchProduct->branch_price !== null) {
            return (float) $branchProduct->branch_price;
        }

        return (float) $this->price;
    }

    /**
     * Get formatted price for branch
     */
    public function getFormattedBranchPrice(int $branchId): string
    {
        return '฿' . number_format($this->getBranchPrice($branchId), 2);
    }

    /**
     * Get image URL
     */
    public function getImageUrl(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    /**
     * Get dimensions as formatted string
     */
    public function getDimensionsString(): string
    {
        if (!$this->dimensions) {
            return 'N/A';
        }

        $dims = $this->dimensions;
        if (isset($dims['length'], $dims['width'], $dims['height'])) {
            return $dims['length'] . ' x ' . $dims['width'] . ' x ' . $dims['height'] . ' cm';
        }

        return 'N/A';
    }

    /**
     * Get formatted weight
     */
    public function getFormattedWeight(): string
    {
        return $this->weight ? $this->weight . ' kg' : 'N/A';
    }

    /**
     * Generate SKU if not provided
     */
    public static function generateSku(string $name, int $categoryId): string
    {
        $categoryCode = strtoupper(substr(str_replace(' ', '', $name), 0, 3));
        $timestamp = now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -3));
        
        return $categoryCode . $categoryId . $timestamp . $random;
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get products available in specific branch
     */
    public function scopeAvailableInBranch($query, int $branchId)
    {
        return $query->whereHas('branchProducts', function($q) use ($branchId) {
            $q->where('branch_id', $branchId)
              ->where('is_available', true);
        });
    }

    /**
     * Scope to search products by name or SKU
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
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
     * Get product status badge for UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Get product status text for UI
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '฿' . number_format($this->price, 2);
    }

    /**
     * Auto-generate SKU if not provided
     */
    public function setSkuAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['sku'] = self::generateSku($this->name ?? 'PROD', $this->category_id ?? 1);
        } else {
            $this->attributes['sku'] = strtoupper($value);
        }
    }

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ProductFactory::new();
    }
} 