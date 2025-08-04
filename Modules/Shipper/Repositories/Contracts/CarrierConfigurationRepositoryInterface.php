<?php

namespace Modules\Shipper\Repositories\Contracts;

interface CarrierConfigurationRepositoryInterface
{
    /**
     * Find configuration by ID
     */
    public function find($id);

    /**
     * Get configurations by carrier
     */
    public function getConfigurationsByCarrier($carrierId);

    /**
     * Get configuration for specific carrier and branch
     */
    public function getConfiguration($carrierId, $branchId = null);

    /**
     * Create new configuration
     */
    public function createConfiguration(array $data);

    /**
     * Update configuration
     */
    public function updateConfiguration($id, array $data);

    /**
     * Delete configuration
     */
    public function deleteConfiguration($id);

    /**
     * Get all configurations for DataTable
     */
    public function getForDataTable($request);

    /**
     * Test configuration connection
     */
    public function testConfiguration($id);

    /**
     * Get global configurations
     */
    public function getGlobalConfigurations();

    /**
     * Get branch-specific configurations
     */
    public function getBranchConfigurations($branchId);
} 