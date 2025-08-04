<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductCategory;
use Modules\Product\Entities\BranchProduct;
use Illuminate\Support\Facades\Auth;

/**
 * ProductSelectionController
 * Purpose: Staff product selection during shipment creation
 * Access Level: Branch Staff
 */
class ProductSelectionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:branch_staff']);
    }

    /**
     * Display available products for selection
     * UI Implementation: Visual grid with product images and quick actions
     * Scope: Products available to staff's branch
     */
    public function catalog(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        
        $query = Product::with(['category', 'branchProducts' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->active()->availableInBranch($branchId);

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Search products
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        $products = $query->ordered()->paginate(24); // Grid layout
        $categories = ProductCategory::active()->ordered()->get();

        // Get quick stats
        $stats = [
            'total_available' => Product::availableInBranch($branchId)->count(),
            'categories_count' => $categories->count()
        ];

        return view('product::staff.products.catalog', compact('products', 'categories', 'stats'));
    }

    /**
     * Select products for shipment
     * Business Logic: Add products to shipment with pricing
     */
    public function select(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        $product = Product::with(['branchProducts' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        // Check if product is available in branch
        if (!$product->isAvailableInBranch($branchId)) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available in your branch.'
            ], 400);
        }

        $branchPrice = $product->getBranchPrice($branchId);
        $totalPrice = $branchPrice * $quantity;

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit,
                'unit_price' => $branchPrice,
                'formatted_unit_price' => '฿' . number_format($branchPrice, 2),
                'quantity' => $quantity,
                'total_price' => $totalPrice,
                'formatted_total_price' => '฿' . number_format($totalPrice, 2),
                'image_url' => $product->getImageUrl(),
                'dimensions' => $product->getDimensionsString(),
                'weight' => $product->getFormattedWeight()
            ],
            'message' => 'Product selected successfully.'
        ]);
    }

    /**
     * Search products
     * Optimization: Basic database search with LIKE queries
     */
    public function search(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search term must be at least 2 characters.'
            ]);
        }

        $products = Product::with(['category', 'branchProducts' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])
        ->active()
        ->availableInBranch($branchId)
        ->search($search)
        ->ordered()
        ->limit(20)
        ->get();

        $results = $products->map(function($product) use ($branchId) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'category' => $product->category->name,
                'unit' => $product->unit,
                'price' => $product->getBranchPrice($branchId),
                'formatted_price' => '฿' . number_format($product->getBranchPrice($branchId), 2),
                'image_url' => $product->getImageUrl(),
                'dimensions' => $product->getDimensionsString(),
                'weight' => $product->getFormattedWeight()
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $results,
            'count' => $results->count()
        ]);
    }

    /**
     * Get product details for selection
     */
    public function getProductDetails(Request $request, Product $product)
    {
        $branchId = Auth::user()->branch_id;
        
        // Check if product is available in branch
        if (!$product->isAvailableInBranch($branchId)) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available in your branch.'
            ], 400);
        }

        $branchProduct = BranchProduct::where('branch_id', $branchId)
            ->where('product_id', $product->id)
            ->first();

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'description' => $product->description,
                'category' => $product->category->name,
                'unit' => $product->unit,
                'price' => $product->getBranchPrice($branchId),
                'formatted_price' => '฿' . number_format($product->getBranchPrice($branchId), 2),
                'global_price' => $product->price,
                'formatted_global_price' => '฿' . number_format($product->price, 2),
                'has_custom_price' => $branchProduct ? $branchProduct->hasCustomPrice() : false,
                'image_url' => $product->getImageUrl(),
                'dimensions' => $product->getDimensionsString(),
                'weight' => $product->getFormattedWeight()
            ]
        ]);
    }

    /**
     * Get products by category for quick selection
     */
    public function getByCategory(Request $request, ProductCategory $category)
    {
        $branchId = Auth::user()->branch_id;
        
        $products = Product::with(['branchProducts' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])
        ->active()
        ->availableInBranch($branchId)
        ->where('category_id', $category->id)
        ->ordered()
        ->get();

        $results = $products->map(function($product) use ($branchId) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit,
                'price' => $product->getBranchPrice($branchId),
                'formatted_price' => '฿' . number_format($product->getBranchPrice($branchId), 2),
                'image_url' => $product->getImageUrl()
            ];
        });

        return response()->json([
            'success' => true,
            'category' => $category->name,
            'products' => $results,
            'count' => $results->count()
        ]);
    }

    /**
     * Calculate total for multiple products
     */
    public function calculateTotal(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $items = $request->input('items', []);

        $total = 0;
        $calculatedItems = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || !$product->isAvailableInBranch($branchId)) {
                continue;
            }

            $quantity = intval($item['quantity'] ?? 1);
            $unitPrice = $product->getBranchPrice($branchId);
            $itemTotal = $unitPrice * $quantity;
            $total += $itemTotal;

            $calculatedItems[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'formatted_unit_price' => '฿' . number_format($unitPrice, 2),
                'total' => $itemTotal,
                'formatted_total' => '฿' . number_format($itemTotal, 2)
            ];
        }

        return response()->json([
            'success' => true,
            'items' => $calculatedItems,
            'total' => $total,
            'formatted_total' => '฿' . number_format($total, 2),
            'item_count' => count($calculatedItems)
        ]);
    }
} 