<?php

namespace Modules\User\Repositories\Contracts;

/**
 * PermissionRepositoryInterface
 * 
 * Purpose: Define permission-specific repository methods  
 * Replaces: PermissionManagementService and PermissionDatatableService
 * Used by: PermissionAdminController
 */
interface PermissionRepositoryInterface
{
    /**
     * Get data for permission DataTable
     * 
     * @param array $params DataTable parameters
     * @return array DataTable response
     */
    public function getDatatablePermissions(array $params);
    
    /**
     * Find permission with roles loaded
     * 
     * @param int $id Permission ID
     * @return mixed Permission model with roles
     */
    public function findWithRoles($id);
    
    /**
     * Get all permissions for dropdown/select
     * 
     * @return mixed Collection of permissions
     */
    public function getAllForSelect();
    
    /**
     * Get permissions grouped by category
     * 
     * @return mixed Permissions grouped by category
     */
    public function getPermissionsByCategory();
} 