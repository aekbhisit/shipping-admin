<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ProductAdminController
 * Purpose: Company admin manages global product catalog
 * Access Level: Company Admin
 */
class ProductAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company_admin']);
    }

    /**
     * Display a listing of products
     * UI Implementation: DataTable with category filter, status filter
     * Search Optimization: Basic database search with LIKE queries
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'active');
        }

        // Basic search with LIKE queries
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        $products = $query->ordered()->paginate(20);
        $categories = ProductCategory::active()->ordered()->get();

        return view('product::admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product
     * UI Implementation: Product form with image upload
     */
    public function create()
    {
        $categories = ProductCategory::active()->ordered()->get();
        
        return view('product::admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product
     * Pricing: Simple fixed prices only
     * Business Logic: Auto-generate SKU if needed
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'category_id', 'name', 'description', 'sku', 'price', 
            'unit', 'sort_order'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image_path'] = $imagePath;
        }

        // Auto-generate SKU if not provided
        if (empty($data['sku'])) {
            $data['sku'] = Product::generateSku($data['name'], $data['category_id']);
        } else {
            $data['sku'] = strtoupper($data['sku']);
        }

        // Default to active
        $data['is_active'] = true;

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product
     * Display: Product info, pricing, branch availability
     */
    public function show($id)
    {
        $product = Product::with(['category', 'branchProducts.branch'])->findOrFail($id);
        
        return view('product::admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     * UI Implementation: Product form with image management
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = ProductCategory::active()->ordered()->get();
        
        return view('product::admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product
     * Audit Trail: Log changes
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'category_id', 'name', 'description', 'sku', 'price', 
            'unit', 'sort_order'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image_path'] = $imagePath;
        }

        // Ensure SKU is uppercase
        $data['sku'] = strtoupper($data['sku']);

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage
     * Implementation: Hard delete or deactivate
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Check if product is being used in any orders/shipments
        // For now, just deactivate
        $product->update(['is_active' => false]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deactivated successfully.');
    }

    /**
     * Toggle product active status
     */
    public function toggleStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $product->update([
            'is_active' => !$product->is_active
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $product->is_active ? 'activated' : 'deactivated'
            ]);
        }

        $statusText = $product->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Product {$statusText} successfully.");
    }

    /**
     * Bulk operations support
     * Basic bulk actions (activate/deactivate, delete)
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
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

        switch ($action) {
            case 'activate':
                $count = Product::whereIn('id', $productIds)->update(['is_active' => true]);
                $message = "{$count} products activated successfully.";
                break;
                
            case 'deactivate':
                $count = Product::whereIn('id', $productIds)->update(['is_active' => false]);
                $message = "{$count} products deactivated successfully.";
                break;
                
            case 'delete':
                $count = Product::whereIn('id', $productIds)->update(['is_active' => false]);
                $message = "{$count} products deleted successfully.";
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Manage product categories
     * UI Implementation: Category management interface
     */
    public function categories()
    {
        $categories = ProductCategory::with('children')
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        return view('product::admin.categories.index', compact('categories'));
    }

    /**
     * Manage branch-specific product availability
     * UI Implementation: Branch product management interface
     */
    public function branchProducts()
    {
        $products = Product::with(['category', 'branchProducts.branch'])
            ->active()
            ->ordered()
            ->get();

        $branches = \Modules\Branch\Entities\Branch::active()->get();

        return view('product::admin.branch-products.index', compact('products', 'branches'));
    }

    /**
     * Get DataTable data for AJAX
     */
    public function getDataTable(Request $request)
    {
        $query = Product::with('category');

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'active');
        }

        // Search
        if ($request->has('search') && $request->search['value'] != '') {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('sku', 'like', "%{$searchValue}%")
                  ->orWhere('description', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = $query->count();
        $filteredRecords = $query->count();

        // Ordering
        if ($request->has('order') && count($request->order) > 0) {
            $orderColumn = $request->order[0]['column'];
            $orderDir = $request->order[0]['dir'];
            
            $columns = ['id', 'name', 'category', 'sku', 'price', 'unit', 'status'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $products = $query->skip($start)->take($length)->get();

        $data = [];
        foreach ($products as $index => $product) {
            $data[] = [
                'DT_RowIndex' => $start + $index + 1,
                'image' => $product->getImageUrl() ? 
                    '<img src="' . $product->getImageUrl() . '" alt="' . $product->name . '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">' : 
                    '<div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bx bx-image text-muted"></i></div>',
                'name' => '<div><h6 class="mb-1">' . $product->name . '</h6>' . 
                         ($product->description ? '<small class="text-muted">' . Str::limit($product->description, 50) . '</small>' : '') . '</div>',
                'category' => $product->category ? $product->category->name : 'No Category',
                'sku' => '<code>' . $product->sku . '</code>',
                'price' => $product->formatted_price,
                'unit' => $product->unit,
                'status' => $product->status_badge,
                'actions' => '<div class="btn-group btn-group-sm" role="group">' .
                           '<a href="' . route('admin.products.show', $product) . '" class="btn btn-outline-info me-1" title="View"><i class="bx bx-show"></i></a>' .
                           '<a href="' . route('admin.products.edit', $product) . '" class="btn btn-outline-primary me-1" title="Edit"><i class="bx bx-edit"></i></a>' .
                           '<button type="button" class="btn btn-outline-' . ($product->is_active ? 'warning' : 'success') . ' toggle-status" data-id="' . $product->id . '" title="' . ($product->is_active ? 'Deactivate' : 'Activate') . '"><i class="bx bx-' . ($product->is_active ? 'ban' : 'check') . '"></i></button>' .
                           '</div>'
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
} 