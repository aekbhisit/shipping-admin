<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\ProductCategory;
use Illuminate\Support\Facades\Validator;

/**
 * CategoryAdminController
 * Purpose: Manage product categories
 * Access Level: Company Admin
 */
class CategoryAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company_admin']);
    }

    /**
     * Display a listing of product categories
     * UI Implementation: Hierarchical tree view with drag-and-drop reordering
     */
    public function index()
    {
        $categories = ProductCategory::with('children')
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        return view('product::admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     * UI Implementation: Category form with parent selection
     */
    public function create()
    {
        $parentCategories = ProductCategory::active()
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        return view('product::admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category
     * Business Logic: Auto-generate slug, handle hierarchy
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['name', 'description', 'parent_id', 'sort_order']);
        $data['is_active'] = $request->has('is_active');
        
        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        ProductCategory::create($data);

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category
     * Display: Category info, subcategories, products count
     */
    public function show(ProductCategory $category)
    {
        $category->load(['children', 'products']);
        
        return view('product::admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category
     * UI Implementation: Category form with parent selection
     */
    public function edit(ProductCategory $category)
    {
        $parentCategories = ProductCategory::active()
            ->where('id', '!=', $category->id)
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        return view('product::admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category
     * Business Logic: Handle hierarchy changes, update slug
     */
    public function update(Request $request, ProductCategory $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['name', 'description', 'parent_id', 'sort_order']);
        $data['is_active'] = $request->has('is_active');
        
        // Update slug if name changed
        if ($data['name'] !== $category->name) {
            $data['slug'] = \Str::slug($data['name']);
        }

        $category->update($data);

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category
     * Business Logic: Check for subcategories and products
     */
    public function destroy(ProductCategory $category)
    {
        // Check if category has subcategories
        if ($category->children()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with subcategories.');
        }

        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with products.');
        }

        $category->delete();

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Reorder categories
     * Business Logic: Update sort order for drag-and-drop
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:product_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->categories as $categoryData) {
            ProductCategory::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categories reordered successfully.'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Categories reordered successfully.');
    }

    /**
     * Toggle category active status
     */
    public function toggleStatus(Request $request, ProductCategory $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $category->is_active ? 'activated' : 'deactivated'
            ]);
        }

        $statusText = $category->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Category {$statusText} successfully.");
    }

    /**
     * Get category tree for AJAX
     */
    public function getCategoryTree()
    {
        $categories = ProductCategory::with('children')
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
} 