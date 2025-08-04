<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\User\Repositories\Contracts\UserRepositoryInterface;
use Modules\User\Entities\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * UserRepository
 * 
 * Purpose: Handle all user-related database operations
 * Replaces: Direct model access in UserAdminController
 * Pattern: Controller → Repository → Database
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $model;

    /**
     * UserRepository constructor.
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Basic CRUD Operations
     */
    public function findById(int $id): ?User
    {
        return $this->model->with(['branch'])->find($id);
    }

    public function create(array $data): User
    {
        $user = $this->model->create($data);
        return $user->load(['branch']);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->findById($id);
        
        if (!$user) {
            throw new ModelNotFoundException("User with ID {$id} not found");
        }

        $user->update($data);
        return $user->fresh(['branch']);
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    /**
     * Branch-specific queries (optimized with basic indexes)
     */
    public function findByBranch(int $branchId): Collection
    {
        return $this->model->with(['branch'])
            ->byBranch($branchId)
            ->orderBy('name')
            ->get();
    }

    public function findActiveByBranch(int $branchId): Collection
    {
        return $this->model->with(['branch'])
            ->active()
            ->byBranch($branchId)
            ->orderBy('name')
            ->get();
    }

    public function findByUserType(string $userType): Collection
    {
        return $this->model->with(['branch'])
            ->byUserType($userType)
            ->orderBy('name')
            ->get();
    }

    public function findBranchAdmins(int $branchId): Collection
    {
        return $this->model->with(['branch'])
            ->branchAdmins()
            ->byBranch($branchId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function findBranchStaff(int $branchId): Collection
    {
        return $this->model->with(['branch'])
            ->branchStaff()
            ->byBranch($branchId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * User type filtering methods
     */
    public function findCompanyAdmins(): Collection
    {
        return $this->model
            ->companyAdmins()
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function findAllBranchAdmins(): Collection
    {
        return $this->model->with(['branch'])
            ->branchAdmins()
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function findAllBranchStaff(): Collection
    {
        return $this->model->with(['branch'])
            ->branchStaff()
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Business queries for integration
     */
    public function getUsersWithPagination(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['branch'])->active();

        // Apply filters
        if (!empty($filters['branch_id'])) {
            $query->byBranch($filters['branch_id']);
        }

        if (!empty($filters['user_type'])) {
            $query->byUserType($filters['user_type']);
        }

        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->inactive();
            }
        }

        if (!empty($filters['created_from'])) {
            $query->where('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->where('created_at', '<=', $filters['created_to']);
        }

        // Default ordering
        $sortField = $filters['sort'] ?? 'name';
        $sortDirection = $filters['direction'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    public function searchUsers(string $searchQuery, array $filters = []): Collection
    {
        $query = $this->model->with(['branch'])->active();

        // Search in name and email
        $searchTerm = '%' . $searchQuery . '%';
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', $searchTerm)
              ->orWhere('email', 'like', $searchTerm);
        });

        // Apply additional filters
        if (!empty($filters['branch_id'])) {
            $query->byBranch($filters['branch_id']);
        }

        if (!empty($filters['user_type'])) {
            $query->byUserType($filters['user_type']);
        }

        return $query->orderBy('name')->limit(50)->get();
    }

    public function getUserActivitySummary(int $userId): array
    {
        $user = $this->findById($userId);
        
        if (!$user) {
            return [];
        }

        return $user->getBranchActivitySummary();
    }

    public function getBranchActivitySummary(int $branchId): array
    {
        $summary = [
            'total_users' => 0,
            'active_users' => 0,
            'inactive_users' => 0,
            'recent_activity_count' => 0,
            'user_types' => [],
        ];

        // Get user counts using optimized queries with indexes
        $userCounts = $this->model
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active'),
                DB::raw('SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive'),
            ])
            ->byBranch($branchId)
            ->first();

        if ($userCounts) {
            $summary['total_users'] = $userCounts->total;
            $summary['active_users'] = $userCounts->active;
            $summary['inactive_users'] = $userCounts->inactive;
        }

        // Get user type breakdown
        $userTypes = $this->model
            ->select(['user_type', DB::raw('COUNT(*) as count')])
            ->byBranch($branchId)
            ->active()
            ->groupBy('user_type')
            ->get()
            ->pluck('count', 'user_type')
            ->toArray();

        $summary['user_types'] = $userTypes;

        // Get recent activity count (last 7 days)
        $recentActivityCount = $this->model
            ->byBranch($branchId)
            ->withRecentActivity(7)
            ->count();

        $summary['recent_activity_count'] = $recentActivityCount;

        return $summary;
    }

    public function canUserAccessBranch(int $userId, int $branchId): bool
    {
        $user = $this->findById($userId);
        
        if (!$user) {
            return false;
        }

        return $user->canAccessBranch($branchId);
    }

    public function softDeleteUser(int $userId, int $deletedBy): bool
    {
        $user = $this->findById($userId);
        $deletedByUser = $this->findById($deletedBy);
        
        if (!$user || !$deletedByUser) {
            return false;
        }

        return $user->deactivate($deletedByUser);
    }

    public function restoreUser(int $userId): bool
    {
        $user = $this->findById($userId);
        
        if (!$user) {
            return false;
        }

        return $user->reactivate();
    }

    /**
     * Advanced filtering and analytics
     */
    public function getUsersByDateRange(\DateTime $startDate, \DateTime $endDate): Collection
    {
        return $this->model->with(['branch'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getInactiveUsers(int $days = 30): Collection
    {
        $cutoffDate = Carbon::now()->subDays($days);
        
        return $this->model->with(['branch'])
            ->where(function ($query) use ($cutoffDate) {
                $query->where('last_branch_activity', '<', $cutoffDate)
                      ->orWhereNull('last_branch_activity');
            })
            ->active()
            ->orderBy('last_branch_activity', 'asc')
            ->get();
    }

    public function getUsersWithoutBranch(): Collection
    {
        return $this->model
            ->whereNull('branch_id')
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function getUserCountByType(): array
    {
        return $this->model
            ->select(['user_type', DB::raw('COUNT(*) as count')])
            ->active()
            ->groupBy('user_type')
            ->get()
            ->pluck('count', 'user_type')
            ->toArray();
    }

    public function getUserCountByBranch(): array
    {
        return $this->model
            ->select(['branch_id', DB::raw('COUNT(*) as count')])
            ->with('branch:id,name')
            ->active()
            ->whereNotNull('branch_id')
            ->groupBy('branch_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->branch->name ?? "Branch #{$item->branch_id}" => $item->count];
            })
            ->toArray();
    }

    /**
     * Permission and role related queries
     */
    public function getUsersWithPermission(string $permission): Collection
    {
        // Since we removed Spatie, return empty collection
        return collect();
    }

    public function getUsersWithRole(string $role): Collection
    {
        // Since we removed Spatie, return empty collection
        return collect();
    }

    public function getBranchManagers(): Collection
    {
        return $this->model->with(['branch'])
            ->whereIn('user_type', [User::USER_TYPE_COMPANY_ADMIN, User::USER_TYPE_BRANCH_ADMIN])
            ->active()
            ->orderBy('user_type')
            ->orderBy('name')
            ->get();
    }

    /**
     * Activity and audit related
     */
    public function getUsersWithRecentActivity(int $days = 7): Collection
    {
        return $this->model->with(['branch'])
            ->withRecentActivity($days)
            ->active()
            ->orderBy('last_branch_activity', 'desc')
            ->get();
    }

    public function getDeactivatedUsers(): Collection
    {
        return $this->model->with(['branch', 'deactivatedBy'])
            ->inactive()
            ->orderBy('deactivated_at', 'desc')
            ->get();
    }

    public function getUsersCreatedBy(int $creatorId): Collection
    {
        return $this->model->with(['branch'])
            ->where('created_by', $creatorId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Helper method to get users accessible by a specific user
     */
    public function getAccessibleUsers(User $user, array $filters = []): Collection
    {
        $query = $this->model->with(['branch'])
            ->accessibleBy($user);

        // Apply additional filters
        if (!empty($filters['user_type'])) {
            $query->byUserType($filters['user_type']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->inactive();
            }
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Bulk operations
     */
    public function bulkUpdateUserType(array $userIds, string $userType): int
    {
        return $this->model->whereIn('id', $userIds)
            ->update(['user_type' => $userType]);
    }

    public function bulkDeactivateUsers(array $userIds, int $deactivatedBy): int
    {
        return $this->model->whereIn('id', $userIds)
            ->update([
                'is_active' => false,
                'deactivated_at' => now(),
                'deactivated_by' => $deactivatedBy,
            ]);
    }

    public function bulkActivateUsers(array $userIds): int
    {
        return $this->model->whereIn('id', $userIds)
            ->update([
                'is_active' => true,
                'deactivated_at' => null,
                'deactivated_by' => null,
            ]);
    }

    /**
     * Get data for user DataTable
     * 
     * @param array $params DataTable parameters
     * @return array DataTable response
     */
    public function getDatatableUsers(array $params)
    {
        $query = $this->model->with(['branch']);

        // Apply search filters
        if (!empty($params['search']['value'])) {
            $searchValue = $params['search']['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhereHas('branch', function($branch) use ($searchValue) {
                      $branch->where('name', 'like', "%{$searchValue}%");
                  });
            });
        }

        // Apply column-specific filters
        if (!empty($params['columns'])) {
            foreach ($params['columns'] as $index => $column) {
                if (!empty($column['search']['value'])) {
                    $searchValue = $column['search']['value'];
                    switch ($index) {
                        case 1: // Name column
                            $query->where('name', 'like', "%{$searchValue}%");
                            break;
                        case 2: // Email column
                            $query->where('email', 'like', "%{$searchValue}%");
                            break;
                        case 3: // User Type column
                            $query->where('user_type', 'like', "%{$searchValue}%");
                            break;
                        case 4: // Branch column
                            $query->whereHas('branch', function($branch) use ($searchValue) {
                                $branch->where('name', 'like', "%{$searchValue}%");
                            });
                            break;
                        case 5: // Status column
                            if (strtolower($searchValue) === 'active') {
                                $query->where('is_active', true);
                            } elseif (strtolower($searchValue) === 'inactive') {
                                $query->where('is_active', false);
                            }
                            break;
                    }
                }
            }
        }

        // Get total count before filters
        $totalRecords = $this->model->count();
        
        // Get filtered count
        $filteredRecords = $query->count();

        // Apply ordering
        if (!empty($params['order'])) {
            foreach ($params['order'] as $order) {
                $columnIndex = $order['column'];
                $direction = $order['dir'];
                
                switch ($columnIndex) {
                    case 0: // ID
                        $query->orderBy('id', $direction);
                        break;
                    case 1: // Name
                        $query->orderBy('name', $direction);
                        break;
                    case 2: // Email
                        $query->orderBy('email', $direction);
                        break;
                    case 3: // User Type
                        $query->orderBy('user_type', $direction);
                        break;
                    case 4: // Branch
                        $query->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
                              ->orderBy('branches.name', $direction)
                              ->select('users.*'); // Ensure we only select user columns
                        break;
                    case 6: // Created At
                        $query->orderBy('created_at', $direction);
                        break;
                    default:
                        $query->orderBy('id', $direction);
                        break;
                }
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        // Apply pagination
        $start = $params['start'] ?? 0;
        $length = $params['length'] ?? 10;
        
        if ($length > 0) {
            $query->offset($start)->limit($length);
        }

        // Get the data
        $data = $query->get();

        // Format data for DataTable
        $formattedData = $data->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->getUserTypeDisplayAttribute(),
                'branch_name' => $user->branch ? $user->branch->name : 'ไม่ระบุ',
                'status' => $user->getStatusDisplayAttribute(),
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : '',
                'actions' => '' // Will be handled in the view
            ];
        });

        return [
            'draw' => intval($params['draw'] ?? 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $formattedData
        ];
    }

    /**
     * Find user with roles loaded
     * 
     * @param int $id User ID
     * @return mixed User model with roles
     */
    public function findWithRoles($id)
    {
        return $this->model->with(['branch'])->find($id);
    }

    /**
     * Create user with role assignment
     * 
     * @param array $userData User data
     * @param array $roleIds Role IDs to assign
     * @return mixed Created user
     */
    public function createUserWithRoles(array $userData, array $roleIds = [])
    {
        DB::beginTransaction();
        
        try {
            // Create the user
            $user = $this->model->create($userData);
            
            DB::commit();
            
            return $user->load(['branch']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update user's roles
     * 
     * @param int $userId User ID
     * @param array $roleIds Role IDs to assign
     * @return bool Success status
     */
    public function updateUserRoles($userId, array $roleIds)
    {
        // Since we removed Spatie, this method is no longer needed
        return true;
    }

    /**
     * Get user statistics
     * 
     * @return array User statistics
     */
    public function getUserStatistics()
    {
        $totalUsers = $this->model->count();
        $activeUsers = $this->model->active()->count();
        $inactiveUsers = $this->model->inactive()->count();
        
        $usersByType = $this->getUserCountByType();
        $usersByBranch = $this->getUserCountByBranch();
        
        // Get users created in the last 30 days
        $recentUsers = $this->model
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        // Get users with recent activity (last 7 days)
        $activeInLastWeek = $this->model
            ->where('last_branch_activity', '>=', Carbon::now()->subDays(7))
            ->count();
        
        // Get users without branch assignment
        $usersWithoutBranch = $this->model
            ->whereNull('branch_id')
            ->count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'users_by_type' => $usersByType,
            'users_by_branch' => $usersByBranch,
            'recent_users' => $recentUsers,
            'active_in_last_week' => $activeInLastWeek,
            'users_without_branch' => $usersWithoutBranch,
            'activity_rate' => $totalUsers > 0 ? round(($activeInLastWeek / $totalUsers) * 100, 2) : 0,
        ];
    }
} 