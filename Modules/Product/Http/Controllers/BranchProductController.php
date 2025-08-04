<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductCategory;
use Modules\Product\Entities\BranchProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * BranchProductController
 * Purpose: Branch admin manages branch-specific product availability
 * Access Level: Branch Admin
 */
class BranchProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:branch_admin', 'branch.isolation']);
    }

    /**
     * View branch product catalog
     * UI Implementation: Visual grid with product images and quick actions
     * Scope: Products available to current branch
     */
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        
        $query = Product::with(['category', 'branchProducts' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->active();

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Availability filter
        if ($request->has('availability') && $request->availability != '') {
            if ($request->availability == 'available') {
                $query->availableInBranch($branchId);
            } elseif ($request->availability == 'unavailable') {
                $query->whereDoesntHave('branchProducts', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                      ->where('is_available', true);
                });
            }
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        $products = $query->ordered()->paginate(24); // Grid layout, more items per page
        $categories = ProductCategory::active()->ordered()->get();

        // Get branch product statistics
        $stats = [
            'total_products' => Product::active()->count(),
            'available_products' => Product::availableInBranch($branchId)->count(),
            'custom_pricing' => BranchProduct::byBranch($branchId)->withCustomPrice()->count()
        ];

        return view('product::branch.products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Toggle product availability for branch
     * Business Logic: Enable/disable products for current branch
     */
    public function toggleAvailability(Request $request, Product $product)
    {
        $branchId = Auth::user()->branch_id;
        
        $branchProduct = BranchProduct::firstOrCreate(
            [
                'branch_id' => $branchId,
                'product_id' => $product->id
            ],
            [
                'is_available' => false,
                'branch_price' => null
            ]
        );

        $branchProduct->update([
            'is_available' => !$branchProduct->is_available
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_available' => $branchProduct->is_available,
                'message' => $branchProduct->is_available ? 
                    'Product enabled for branch' : 
                    'Product disabled for branch'
            ]);
        }

        $message = $branchProduct->is_available ? 
            'Product enabled for your branch successfully.' : 
            'Product disabled for your branch successfully.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Update branch-specific pricing
     * Pricing: Override global price for branch
     */
    public function updatePrice(Request $request, Product $product)
    {
        $branchId = Auth::user()->branch_id;
        
        $validator = Validator::make($request->all(), [
            'branch_price' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $branchProduct = BranchProduct::firstOrCreate(
            [
                'branch_id' => $branchId,
                'product_id' => $product->id
            ],
            [
                'is_available' => true,
                'branch_price' => null
            ]
        );

        $branchPrice = $request->branch_price === '' ? null : $request->branch_price;
        
        $branchProduct->update([
            'branch_price' => $branchPrice
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'branch_price' => $branchPrice,
                'formatted_price' => $branchPrice ? '฿' . number_format($branchPrice, 2) : null,
                'effective_price' => $branchProduct->getFormattedEffectivePrice(),
                'pricing_type' => $branchProduct->pricing_type,
                'message' => $branchPrice ? 
                    'Custom price set successfully' : 
                    'Price reset to global price'
            ]);
        }

        $message = $branchPrice ? 
            'Custom price set successfully.' : 
            'Price reset to global price successfully.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Bulk operations for branch products
     */
    public function bulkAction(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:enable,disable,reset_price',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $productIds = $request->product_ids;
        $action = $request->action;
        $count = 0;

        foreach ($productIds as $productId) {
            $branchProduct = BranchProduct::firstOrCreate(
                [
                    'branch_id' => $branchId,
                    'product_id' => $productId
                ],
                [
                    'is_available' => false,
                    'branch_price' => null
                ]
            );

            switch ($action) {
                case 'enable':
                    $branchProduct->update(['is_available' => true]);
                    $count++;
                    break;
                    
                case 'disable':
                    $branchProduct->update(['is_available' => false]);
                    $count++;
                    break;
                    
                case 'reset_price':
                    $branchProduct->update(['branch_price' => null]);
                    $count++;
                    break;
            }
        }

        $messages = [
            'enable' => "{$count} products enabled for your branch.",
            'disable' => "{$count} products disabled for your branch.",
            'reset_price' => "{$count} product prices reset to global pricing."
        ];

        return redirect()->back()->with('success', $messages[$action]);
    }

    /**
     * Show product details for branch
     */
    public function show(Product $product)
    {
        $branchId = Auth::user()->branch_id;
        
        $product->load(['category', 'branchProducts' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }]);

        $branchProduct = $product->branchProducts->first();

        return view('product::branch.products.show', compact('product', 'branchProduct'));
    }

    /**
     * Get branch product data for AJAX
     */
    public function getBranchProductData(Request $request, Product $product)
    {
        $branchId = Auth::user()->branch_id;
        
        $branchProduct = BranchProduct::where('branch_id', $branchId)
            ->where('product_id', $product->id)
            ->first();

        return response()->json([
            'success' => true,
            'is_available' => $branchProduct ? $branchProduct->is_available : false,
            'branch_price' => $branchProduct ? $branchProduct->branch_price : null,
            'formatted_branch_price' => $branchProduct && $branchProduct->branch_price ? 
                '฿' . number_format($branchProduct->branch_price, 2) : null,
            'effective_price' => $branchProduct ? 
                $branchProduct->getFormattedEffectivePrice() : 
                '฿' . number_format($product->price, 2),
            'global_price' => '฿' . number_format($product->price, 2),
            'pricing_type' => $branchProduct ? $branchProduct->pricing_type : 'Global'
        ]);
    }
} 