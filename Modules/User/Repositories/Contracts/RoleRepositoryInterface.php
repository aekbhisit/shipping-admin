<?php

namespace Modules\User\Repositories\Contracts;

/**
 * RoleRepositoryInterface
 * 
 * Purpose: Define role-specific repository methods
 * Replaces: RoleManagementService and RoleDatatableService
 * Used by: RoleAdminController
 */
interface RoleRepositoryInterface
{
    /**
     * Get data for role DataTable
     * 
     * @param array $params DataTable parameters
     * @return array DataTable response
     */
    public function getDatatableRoles(array $params);
    
    /**
     * Find role with permissions loaded
     * 
     * @param int $id Role ID
     * @return mixed Role model with permissions
     */
    public function findWithPermissions($id);
    
    /**
     * Get all roles for dropdown/select
     * 
     * @return mixed Collection of roles
     */
    public function getAllForSelect();
    
    /**
     * Create role with permission assignment
     * 
     * @param array $roleData Role data
     * @param array $permissionIds Permission IDs to assign
     * @return mixed Created role
     */
    public function createRoleWithPermissions(array $roleData, array $permissionIds = []);
    
    /**
     * Update role's permissions
     * 
     * @param int $roleId Role ID
     * @param array $permissionIds Permission IDs to assign
     * @return bool Success status
     */
    public function updateRolePermissions($roleId, array $permissionIds);
} 