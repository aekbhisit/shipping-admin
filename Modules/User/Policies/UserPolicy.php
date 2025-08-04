<?php

namespace Modules\User\Policies;

use Modules\User\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Company Admin can view all users
        if ($user->isCompanyAdmin()) {
            return $user->can('users.view_all');
        }

        // Branch Admin can view users in their branch
        if ($user->isBranchAdmin()) {
            return $user->can('users.view_branch') && $user->branch_id !== null;
        }

        // Branch Staff can view users in their branch (limited)
        if ($user->isBranchStaff()) {
            return $user->can('users.view_branch') && $user->branch_id !== null;
        }

        return false;
    }

    /**
     * Determine whether the user can view the specific user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function view(User $user, User $targetUser): bool
    {
        // Users can always view their own profile
        if ($user->id === $targetUser->id) {
            return true;
        }

        // Company Admin can view all users
        if ($user->isCompanyAdmin()) {
            return $user->can('users.view_all');
        }

        // Branch Admin and Staff can view users in same branch
        if ($user->branch_id && $user->branch_id === $targetUser->branch_id) {
            return $user->can('users.view_branch');
        }

        return false;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Company Admin can create any type of user
        if ($user->isCompanyAdmin()) {
            return $user->can('users.manage_all');
        }

        // Branch Admin can create branch staff in their branch
        if ($user->isBranchAdmin() && $user->branch_id) {
            return $user->can('users.manage_branch');
        }

        // Branch Staff cannot create users
        return false;
    }

    /**
     * Determine whether the user can update the specific user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function update(User $user, User $targetUser): bool
    {
        // Users cannot edit their own role/branch assignments
        if ($user->id === $targetUser->id) {
            return false; // Handled separately in profile management
        }

        // Company Admin can update all users
        if ($user->isCompanyAdmin()) {
            return $user->can('users.manage_all');
        }

        // Branch Admin can update users in same branch (except company admins)
        if ($user->isBranchAdmin() && 
            $user->branch_id && 
            $user->branch_id === $targetUser->branch_id &&
            !$targetUser->isCompanyAdmin()) {
            return $user->can('users.manage_branch');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the specific user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function delete(User $user, User $targetUser): bool
    {
        // Users cannot delete themselves
        if ($user->id === $targetUser->id) {
            return false;
        }

        // Only Company Admin can delete users
        if ($user->isCompanyAdmin()) {
            // Cannot delete the last company admin
            if ($targetUser->isCompanyAdmin()) {
                $activeAdminCount = User::companyAdmins()->active()->count();
                return $activeAdminCount > 1 && $user->can('users.manage_all');
            }
            
            return $user->can('users.manage_all');
        }

        return false;
    }

    /**
     * Determine whether the user can restore the specific user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function restore(User $user, User $targetUser): bool
    {
        // Only Company Admin can restore users
        return $user->isCompanyAdmin() && $user->can('users.manage_all');
    }

    /**
     * Determine whether the user can permanently delete the specific user.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function forceDelete(User $user, User $targetUser): bool
    {
        // Permanent deletion not allowed for compliance
        return false;
    }

    /**
     * Determine whether the user can assign branches to users.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function assignBranch(User $user, User $targetUser): bool
    {
        // Only Company Admin can assign branches
        if (!$user->isCompanyAdmin()) {
            return false;
        }

        // Cannot assign branch to company admin
        if ($targetUser->isCompanyAdmin()) {
            return false;
        }

        return $user->can('users.manage_all');
    }

    /**
     * Determine whether the user can change roles for users.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function changeRole(User $user, User $targetUser): bool
    {
        // Users cannot change their own role
        if ($user->id === $targetUser->id) {
            return false;
        }

        // Only Company Admin can change roles
        return $user->isCompanyAdmin() && $user->can('users.manage_all');
    }

    /**
     * Determine whether the user can manage permissions for users.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function managePermissions(User $user, User $targetUser): bool
    {
        // Only Company Admin can manage permissions
        return $user->isCompanyAdmin() && $user->can('users.manage_all');
    }

    /**
     * Determine whether the user can view user activity logs.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function viewActivityLogs(User $user, User $targetUser): bool
    {
        // Users can view their own activity logs
        if ($user->id === $targetUser->id) {
            return true;
        }

        // Company Admin can view all activity logs
        if ($user->isCompanyAdmin()) {
            return $user->can('audit.view_all');
        }

        // Branch Admin can view activity logs for users in same branch
        if ($user->isBranchAdmin() && 
            $user->branch_id && 
            $user->branch_id === $targetUser->branch_id) {
            return $user->can('audit.view_branch');
        }

        return false;
    }

    /**
     * Determine whether the user can export user data.
     *
     * @param User $user
     * @return bool
     */
    public function export(User $user): bool
    {
        // Company Admin can export all user data
        if ($user->isCompanyAdmin()) {
            return $user->can('users.manage_all');
        }

        // Branch Admin can export data for their branch
        if ($user->isBranchAdmin() && $user->branch_id) {
            return $user->can('users.manage_branch');
        }

        return false;
    }

    /**
     * Determine whether the user can access user management dashboard.
     *
     * @param User $user
     * @return bool
     */
    public function accessDashboard(User $user): bool
    {
        // Company Admin can access full dashboard
        if ($user->isCompanyAdmin()) {
            return $user->can('users.view_all');
        }

        // Branch Admin can access branch dashboard
        if ($user->isBranchAdmin() && $user->branch_id) {
            return $user->can('users.view_branch');
        }

        return false;
    }

    /**
     * Determine whether the user can switch branch context.
     *
     * @param User $user
     * @return bool
     */
    public function switchBranchContext(User $user): bool
    {
        // Only Company Admin can switch branch context
        return $user->isCompanyAdmin() && $user->can('branches.access_all');
    }

    /**
     * Determine whether the user can access specific branch data.
     *
     * @param User $user
     * @param int $branchId
     * @return bool
     */
    public function accessBranch(User $user, int $branchId): bool
    {
        return $user->canAccessBranch($branchId);
    }

    /**
     * Determine whether the user can bulk update users.
     *
     * @param User $user
     * @return bool
     */
    public function bulkUpdate(User $user): bool
    {
        // Only Company Admin can perform bulk operations
        return $user->isCompanyAdmin() && $user->can('users.manage_all');
    }

    /**
     * Determine whether the user can impersonate other users.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function impersonate(User $user, User $targetUser): bool
    {
        // Impersonation not allowed for security reasons
        return false;
    }

    /**
     * Determine whether the user can view user statistics.
     *
     * @param User $user
     * @return bool
     */
    public function viewStatistics(User $user): bool
    {
        // Company Admin can view all statistics
        if ($user->isCompanyAdmin()) {
            return $user->can('users.view_all');
        }

        // Branch Admin can view branch statistics
        if ($user->isBranchAdmin() && $user->branch_id) {
            return $user->can('users.view_branch');
        }

        return false;
    }

    /**
     * Determine whether the user can manage user sessions.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function manageSessions(User $user, User $targetUser): bool
    {
        // Users can manage their own sessions
        if ($user->id === $targetUser->id) {
            return true;
        }

        // Only Company Admin can manage other users' sessions
        return $user->isCompanyAdmin() && $user->can('users.manage_all');
    }

    /**
     * Determine whether the user can reset passwords for other users.
     *
     * @param User $user
     * @param User $targetUser
     * @return bool
     */
    public function resetPassword(User $user, User $targetUser): bool
    {
        // Users cannot reset their own password through admin interface
        if ($user->id === $targetUser->id) {
            return false; // Use separate password reset flow
        }

        // Company Admin can reset any password
        if ($user->isCompanyAdmin()) {
            return $user->can('users.manage_all');
        }

        // Branch Admin can reset passwords for users in same branch (except company admins)
        if ($user->isBranchAdmin() && 
            $user->branch_id && 
            $user->branch_id === $targetUser->branch_id &&
            !$targetUser->isCompanyAdmin()) {
            return $user->can('users.manage_branch');
        }

        return false;
    }
} 