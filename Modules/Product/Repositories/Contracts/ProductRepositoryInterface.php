<?php

namespace Modules\Product\Repositories\Contracts;

interface ProductRepositoryInterface
{
    /**
     * Get products data for DataTable
     * 
     * @param array $params
     * @return mixed
     */
    public function getDatatableProducts(array $params = []);

    /**
     * Find product by ID
     * 
     * @param int $id
     * @return \Modules\Product\Entities\Product|null
     */
    public function findById(int $id);

    /**
     * Create new product
     * 
     * @param array $data
     * @return \Modules\Product\Entities\Product
     */
    public function create(array $data);

    /**
     * Update existing product
     * 
     * @param int $id
     * @param array $data
     * @return \Modules\Product\Entities\Product
     */
    public function update(int $id, array $data);

    /**
     * Delete product
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id);

    /**
     * Update product status
     * 
     * @param int $id
     * @param bool $status
     * @return bool
     */
    public function updateStatus(int $id, bool $status);

    /**
     * Get all active products
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveProducts();

    /**
     * Search products by name
     * 
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchProducts(string $term);
} 