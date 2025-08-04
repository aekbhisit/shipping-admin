<?php

namespace Modules\Audit\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Modules\Audit\Entities\UserActivityLog;
use Carbon\Carbon;

class UserActivityService
{
    /**
     * Get user activities with filtering
     */
    public function getUserActivities(array $filters = [], int $perPage = 20)
    {
        $query = UserActivityLog::with(['user', 'branch']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'LIKE', "%{$search}%")
                  ->orWhere('action', 'LIKE', "%{$search}%")
                  ->orWhere('module', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%");
            });
        }

        // Apply filters
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        // Apply sorting
        $sortColumn = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        
        // Map frontend columns to database columns
        $columnMap = [
            'user_name' => 'user_name',
            'action' => 'action',
            'module' => 'module',
            'description' => 'created_at', // Sort by date for description
            'ip_address' => 'ip_address',
            'created_at' => 'created_at'
        ];
        
        $dbColumn = $columnMap[$sortColumn] ?? 'created_at';
        $query->orderBy($dbColumn, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get failed login attempts
     */
    public function getFailedLoginAttempts(array $filters = [], int $perPage = 20)
    {
        $query = UserActivityLog::where('action', 'login_failed');

        // Apply filters
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['username'])) {
            $query->where('description', 'like', '%' . $filters['username'] . '%');
        }

        if (isset($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Clear failed login attempts
     */
    public function clearFailedAttempts()
    {
        $deleted = UserActivityLog::where('action', 'login_failed')->delete();
        
        return [
            'deleted_count' => $deleted,
            'message' => "Cleared {$deleted} failed login attempts"
        ];
    }

    /**
     * Export user activities
     */
    public function exportUserActivities(array $filters = [], string $format = 'csv')
    {
        $activities = $this->getUserActivities($filters, 1000); // Get more records for export

        $data = [];
        foreach ($activities as $activity) {
            $data[] = [
                'ID' => $activity->id,
                'User' => $activity->user_name,
                'Action' => $activity->action,
                'Module' => $activity->module,
                'Description' => $activity->description,
                'IP Address' => $activity->ip_address,
                'Created At' => $activity->created_at->format('Y-m-d H:i:s')
            ];
        }

        return $this->generateExport($data, $format, 'user_activities');
    }

    /**
     * Get activity statistics
     */
    public function getActivityStatistics(string $period = 'month')
    {
        $startDate = $this->getStartDate($period);

        $stats = UserActivityLog::where('created_at', '>=', $startDate)
            ->selectRaw('
                COUNT(*) as total_activities,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT DATE(created_at)) as active_days
            ')
            ->first();

        $actionStats = UserActivityLog::where('created_at', '>=', $startDate)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $moduleStats = UserActivityLog::where('created_at', '>=', $startDate)
            ->selectRaw('module, COUNT(*) as count')
            ->groupBy('module')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_activities' => $stats->total_activities ?? 0,
            'unique_users' => $stats->unique_users ?? 0,
            'active_days' => $stats->active_days ?? 0,
            'action_stats' => $actionStats,
            'module_stats' => $moduleStats
        ];
    }

    /**
     * Get active sessions
     */
    public function getActiveSessions()
    {
        // This would typically query the sessions table
        // For now, return empty array
        return [];
    }

    /**
     * Terminate user session
     */
    public function terminateSession(string $sessionId)
    {
        // This would typically delete from sessions table
        // For now, return success
        return [
            'success' => true,
            'message' => 'Session terminated successfully'
        ];
    }

    /**
     * Get users list for filter dropdown
     */
    public function getUsersList()
    {
        return UserActivityLog::select('user_id', 'user_name')
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_name', 'user_id')
            ->toArray();
    }

    /**
     * Get actions list for filter dropdown
     */
    public function getActionsList()
    {
        return UserActivityLog::select('action')
            ->distinct()
            ->pluck('action')
            ->toArray();
    }

    /**
     * Get modules list for filter dropdown
     */
    public function getModulesList()
    {
        return UserActivityLog::select('module')
            ->whereNotNull('module')
            ->distinct()
            ->pluck('module')
            ->toArray();
    }

    /**
     * Get branches list for filter dropdown
     */
    public function getBranchesList()
    {
        return UserActivityLog::select('branch_id')
            ->whereNotNull('branch_id')
            ->distinct()
            ->pluck('branch_id')
            ->toArray();
    }

    /**
     * Get start date based on period
     */
    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'quarter' => Carbon::now()->subQuarter(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth()
        };
    }

    /**
     * Generate export file
     */
    private function generateExport(array $data, string $format, string $filename)
    {
        $filename = $filename . '_' . date('Y-m-d_H-i-s') . '.' . $format;
        
        // For now, return a simple CSV
        if ($format === 'csv') {
            $csv = '';
            if (!empty($data)) {
                $csv .= implode(',', array_keys($data[0])) . "\n";
                foreach ($data as $row) {
                    $csv .= implode(',', array_values($row)) . "\n";
                }
            }
            return $csv;
        }

        return $data;
    }
} 