<?php

namespace Modules\Shipper\Repositories\Contracts;

interface CarrierRepositoryInterface
{
    /**
     * Get all carriers
     */
    public function all();

    /**
     * Get all active carriers ordered by priority
     */
    public function getActiveByPriority();

    /**
     * Find carrier by ID
     */
    public function find($id);

    /**
     * Find carrier by code
     */
    public function findByCode($code);

    /**
     * Create new carrier
     */
    public function create(array $data);

    /**
     * Update carrier
     */
    public function update($id, array $data);

    /**
     * Delete carrier
     */
    public function delete($id);

    /**
     * Get carriers with active configurations for branch
     */
    public function getWithActiveConfiguration($branchId = null);

    /**
     * Get carrier API success rate
     */
    public function getSuccessRate($carrierId, $hours = 24);

    /**
     * Update carrier status
     */
    public function updateStatus($id, $isActive);

    /**
     * Get carriers for DataTable
     */
    public function getForDataTable($request);

    /**
     * Bulk update priority order
     */
    public function bulkUpdatePriority(array $priorities);

    /**
     * Get carrier statistics
     */
    public function getStatistics($carrierId = null);
} 