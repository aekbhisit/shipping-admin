<?php

namespace Modules\Product\Repositories\Eloquent;

use Modules\Product\Entities\Product;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * Get products data for DataTable
     */
    public function getDatatableProducts(array $params = [])
    {
        $query = $this->model->query();

        // Apply search if provided
        if (isset($params['search']['value']) && !empty($params['search']['value'])) {
            $searchTerm = $params['search']['value'];
            $query->search($searchTerm);
        }

        return DataTables::of($query)
            ->addColumn('image', function ($product) {
                if ($product->image) {
                    return '<img src="' . asset($product->image) . '" 
                            class="img-thumbnail" 
                            style="width: 50px; height: 50px; object-fit: cover;" 
                            alt="' . $product->name . '">';
                }
                return '<div class="bg-light d-flex align-items-center justify-content-center" 
                        style="width: 50px; height: 50px; border-radius: 4px;">
                        <i class="fas fa-image text-muted"></i>
                        </div>';
            })
            ->addColumn('price_formatted', function ($product) {
                return '฿' . number_format($product->price, 2);
            })
            ->addColumn('cost_formatted', function ($product) {
                return $product->cost ? '฿' . number_format($product->cost, 2) : '-';
            })
            ->addColumn('status_badge', function ($product) {
                return $product->status 
                    ? '<span class="badge bg-success">Active</span>' 
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($product) {
                $actions = '';
                
                // Edit button
                $actions .= '<a href="' . route('admin.product.form', $product->id) . '" 
                            class="btn btn-sm btn-primary me-1" 
                            data-bs-toggle="tooltip" 
                            title="Edit Product">
                            <i class="fas fa-edit"></i>
                            </a>';
                
                // Status toggle button
                $statusClass = $product->status ? 'btn-warning' : 'btn-success';
                $statusIcon = $product->status ? 'fa-eye-slash' : 'fa-eye';
                $statusTitle = $product->status ? 'Deactivate' : 'Activate';
                
                $actions .= '<button type="button" 
                            class="btn btn-sm ' . $statusClass . ' me-1 btn-status" 
                            data-id="' . $product->id . '" 
                            data-status="' . ($product->status ? 0 : 1) . '"
                            data-bs-toggle="tooltip" 
                            title="' . $statusTitle . '">
                            <i class="fas ' . $statusIcon . '"></i>
                            </button>';
                
                // Delete button
                $actions .= '<button type="button" 
                            class="btn btn-sm btn-danger btn-delete" 
                            data-id="' . $product->id . '"
                            data-bs-toggle="tooltip" 
                            title="Delete Product">
                            <i class="fas fa-trash"></i>
                            </button>';
                
                return $actions;
            })
            ->rawColumns(['image', 'status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Find product by ID
     */
    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * Create new product
     */
    public function create(array $data)
    {
        // Generate slug from name
        if (isset($data['name'])) {
            $data['slug'] = createSlugText($data['name']);
        }

        return $this->model->create($data);
    }

    /**
     * Update existing product
     */
    public function update(int $id, array $data)
    {
        $product = $this->findById($id);
        
        if (!$product) {
            throw new \Exception('Product not found');
        }

        // Generate slug from name if name is updated
        if (isset($data['name'])) {
            $data['slug'] = createSlugText($data['name']);
        }

        $product->update($data);
        return $product->fresh();
    }

    /**
     * Delete product
     */
    public function delete(int $id)
    {
        $product = $this->findById($id);
        
        if (!$product) {
            return false;
        }

        // Delete image file if exists
        if ($product->image) {
            $imagePath = str_replace('storage', 'public', $product->image);
            Storage::delete($imagePath);
        }

        return $product->delete();
    }

    /**
     * Update product status
     */
    public function updateStatus(int $id, bool $status)
    {
        $product = $this->findById($id);
        
        if (!$product) {
            return false;
        }

        $product->status = $status;
        return $product->save();
    }

    /**
     * Get all active products
     */
    public function getActiveProducts()
    {
        return $this->model->active()->orderBy('name', 'asc')->get();
    }

    /**
     * Search products by name
     */
    public function searchProducts(string $term)
    {
        return $this->model->search($term)->get();
    }
} 