<?php

namespace Modules\User\Repositories\Contracts;

use Modules\User\Entities\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * UserRepositoryInterface
 * 
 * Purpose: Define user-specific repository methods
 * Replaces: UserDatatableService and direct model access
 * Used by: UserAdminController
 */
interface UserRepositoryInterface
{
    /**
     * Basic CRUD Operations
     */
    public function findById(int $id): ?User;
    
    public function create(array $data): User;
    
    public function update(int $id, array $data): User;
    
    public function delete(int $id): bool;

    /**
     * Branch-specific queries
     */
    public function findByBranch(int $branchId): Collection;
    
    public function findActiveByBranch(int $branchId): Collection;
    
    public function findByUserType(string $userType): Collection;
    
    public function findBranchAdmins(int $branchId): Collection;
    
    public function findBranchStaff(int $branchId): Collection;

    /**
     * User type filtering methods
     */
    public function findCompanyAdmins(): Collection;
    
    public function findAllBranchAdmins(): Collection;
    
    public function findAllBranchStaff(): Collection;

    /**
     * Business queries for integration
     */
    public function getUsersWithPagination(array $filters = []): LengthAwarePaginator;
    
    public function searchUsers(string $query, array $filters = []): Collection;
    
    public function getUserActivitySummary(int $userId): array;
    
    public function getBranchActivitySummary(int $branchId): array;
    
    public function canUserAccessBranch(int $userId, int $branchId): bool;
    
    public function softDeleteUser(int $userId, int $deletedBy): bool;
    
    public function restoreUser(int $userId): bool;

    /**
     * Advanced filtering and analytics
     */
    public function getUsersByDateRange(\DateTime $startDate, \DateTime $endDate): Collection;
    
    public function getInactiveUsers(int $days = 30): Collection;
    
    public function getUsersWithoutBranch(): Collection;
    
    public function getUserCountByType(): array;
    
    public function getUserCountByBranch(): array;

    /**
     * Permission and role related queries
     */
    public function getUsersWithPermission(string $permission): Collection;
    
    public function getUsersWithRole(string $role): Collection;
    
    public function getBranchManagers(): Collection;

    /**
     * Activity and audit related
     */
    public function getUsersWithRecentActivity(int $days = 7): Collection;
    
    public function getDeactivatedUsers(): Collection;
    
    public function getUsersCreatedBy(int $creatorId): Collection;

    /**
     * Get data for user DataTable
     * 
     * @param array $params DataTable parameters
     * @return array DataTable response
     */
    public function getDatatableUsers(array $params);
    
    /**
     * Find user with roles loaded
     * 
     * @param int $id User ID
     * @return mixed User model with roles
     */
    public function findWithRoles($id);
    
    /**
     * Create user with role assignment
     * 
     * @param array $userData User data
     * @param array $roleIds Role IDs to assign
     * @return mixed Created user
     */
    public function createUserWithRoles(array $userData, array $roleIds = []);
    
    /**
     * Update user's roles
     * 
     * @param int $userId User ID
     * @param array $roleIds Role IDs to assign
     * @return bool Success status
     */
    public function updateUserRoles($userId, array $roleIds);
    
    /**
     * Get user statistics
     * 
     * @return array User statistics
     */
    public function getUserStatistics();
} 