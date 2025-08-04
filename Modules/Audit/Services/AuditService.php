<?php

namespace Modules\Audit\Services;

use Modules\Audit\Entities\AuditLog;
use Modules\Audit\Entities\UserActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * AuditService
 * Purpose: Core audit logging functionality and search operations
 */
class AuditService
{
    /**
     * Log data changes for any model
     */
    public function logDataChange(string $model, int $id, string $event, array $oldData, array $newData): void
    {
        // Determine changed fields for updates
        $changedFields = [];
        if ($event === 'updated') {
            $changedFields = array_keys(array_diff_assoc($newData, $oldData));
        } elseif ($event === 'created') {
            $changedFields = array_keys($newData);
        } elseif ($event === 'deleted') {
            $changedFields = array_keys($oldData);
        }

        // Protect sensitive data
        $protectedOldData = $this->protectSensitiveData($oldData);
        $protectedNewData = $this->protectSensitiveData($newData);

        AuditLog::create([
            'auditable_type' => $model,
            'auditable_id' => $id,
            'event_type' => $event,
            'old_values' => $protectedOldData,
            'new_values' => $protectedNewData,
            'changed_fields' => $changedFields,
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()?->branch_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'created_at' => now()
        ]);
    }

    /**
     * Log user activity (login, logout, etc.)
     */
    public function logUserActivity(int $userId, string $activity, array $context = []): void
    {
        $description = $this->generateActivityDescription($activity, $context);

        UserActivityLog::create([
            'user_id' => $userId,
            'branch_id' => auth()->user()?->branch_id ?? data_get($context, 'branch_id'),
            'activity_type' => $activity,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'additional_data' => $context,
            'created_at' => now()
        ]);
    }

    /**
     * Get audit logs with filtering and pagination
     */
    public function getAuditLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = AuditLog::query()->with(['user', 'branch']);

        // Apply filters
        $this->applyAuditFilters($query, $filters);

        // Apply branch scope for branch admins
        if (auth()->user() && auth()->user()->role === 'branch_admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        // Apply sorting
        $sortColumn = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        
        // Map frontend columns to database columns
        $columnMap = [
            'user_name' => 'user_id',
            'event_type' => 'event_type',
            'model_type' => 'auditable_type',
            'description' => 'created_at', // Sort by date for description
            'ip_address' => 'ip_address',
            'created_at' => 'created_at'
        ];
        
        $dbColumn = $columnMap[$sortColumn] ?? 'created_at';
        $query->orderBy($dbColumn, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Search audit logs with advanced filtering
     */
    public function searchAuditLogs(string $query, array $filters = []): Collection
    {
        $auditQuery = AuditLog::query()->with(['user', 'branch']);

        // Apply text search
        if (!empty($query)) {
            $auditQuery->where(function($q) use ($query) {
                $q->whereHas('user', function($userQuery) use ($query) {
                    $userQuery->where('name', 'LIKE', "%{$query}%")
                             ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->orWhere('auditable_type', 'LIKE', "%{$query}%")
                ->orWhere('event_type', 'LIKE', "%{$query}%")
                ->orWhere('url', 'LIKE', "%{$query}%")
                ->orWhereJsonContains('changed_fields', $query);
            });
        }

        // Apply additional filters
        $this->applyAuditFilters($auditQuery, $filters);

        // Apply branch scope for branch admins
        if (auth()->user() && auth()->user()->role === 'branch_admin') {
            $auditQuery->where('branch_id', auth()->user()->branch_id);
        }

        return $auditQuery->orderBy('created_at', 'desc')->limit(100)->get();
    }

    /**
     * Get user activity logs with filtering
     */
    public function getUserActivityLogs(int $userId, array $filters = []): Collection
    {
        $query = UserActivityLog::query()->with(['user', 'branch'])
            ->where('user_id', $userId);

        // Apply branch scope for branch admins
        if (auth()->user() && auth()->user()->role === 'branch_admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        // Apply date filter if provided
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from']));
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        // Apply activity type filter
        if (!empty($filters['activity_type'])) {
            $query->where('activity_type', $filters['activity_type']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get branch activity summary
     */
    public function getBranchActivitySummary(int $branchId, array $dateRange = []): array
    {
        $fromDate = !empty($dateRange['from']) ? Carbon::parse($dateRange['from']) : now()->subDays(30);
        $toDate = !empty($dateRange['to']) ? Carbon::parse($dateRange['to']) : now();

        // Get audit logs for the branch
        $auditLogs = AuditLog::where('branch_id', $branchId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        // Get user activity logs for the branch
        $activityLogs = UserActivityLog::where('branch_id', $branchId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        // Calculate statistics
        $uniqueUsers = $activityLogs->pluck('user_id')->unique()->count();
        $totalLogins = $activityLogs->where('activity_type', 'login')->count();
        $failedAttempts = $activityLogs->where('activity_type', 'failed_login')->count();
        $dataChanges = $auditLogs->count();

        // Group by event type
        $eventTypeCounts = $auditLogs->groupBy('event_type')->map->count();

        // Group by model type
        $modelTypeCounts = $auditLogs->groupBy(function($log) {
            return class_basename($log->auditable_type);
        })->map->count();

        // Daily activity trend
        $dailyTrend = $activityLogs->groupBy(function($log) {
            return $log->created_at->format('Y-m-d');
        })->map->count();

        return [
            'summary' => [
                'unique_users' => $uniqueUsers,
                'total_logins' => $totalLogins,
                'failed_attempts' => $failedAttempts,
                'data_changes' => $dataChanges,
                'date_range' => [
                    'from' => $fromDate->format('Y-m-d'),
                    'to' => $toDate->format('Y-m-d')
                ]
            ],
            'event_types' => $eventTypeCounts,
            'model_types' => $modelTypeCounts,
            'daily_trend' => $dailyTrend,
            'security_metrics' => [
                'login_success_rate' => $totalLogins > 0 ? 
                    round(($totalLogins / ($totalLogins + $failedAttempts)) * 100, 2) : 100,
                'failed_attempt_rate' => $totalLogins > 0 ?
                    round(($failedAttempts / ($totalLogins + $failedAttempts)) * 100, 2) : 0
            ]
        ];
    }

    /**
     * Get recent audit activity
     */
    public function getRecentActivity(int $hours = 24, int $limit = 50): Collection
    {
        $query = AuditLog::query()->with(['user', 'branch'])
            ->where('created_at', '>=', now()->subHours($hours));

        // Apply branch scope for branch admins
        if (auth()->user() && auth()->user()->role === 'branch_admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        return $query->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    /**
     * Get audit statistics
     */
    public function getAuditStatistics(): array
    {
        $query = AuditLog::query();

        // Apply branch scope for branch admins
        if (auth()->user() && auth()->user()->role === 'branch_admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $totalLogs = $query->count();
        $todayLogs = $query->whereDate('created_at', today())->count();
        $weekLogs = $query->where('created_at', '>=', now()->subWeek())->count();

        // Event type distribution
        $eventTypes = $query->selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        // Model type distribution
        $modelTypes = $query->selectRaw('auditable_type, COUNT(*) as count')
            ->groupBy('auditable_type')
            ->pluck('count', 'auditable_type')
            ->map(function($count, $type) {
                return ['count' => $count, 'name' => class_basename($type)];
            })
            ->toArray();

        // Top users by activity
        $topUsers = $query->with('user')
            ->selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($log) {
                return [
                    'user' => $log->user?->name ?? 'Unknown',
                    'count' => $log->count
                ];
            });

        return [
            'totals' => [
                'all_time' => $totalLogs,
                'today' => $todayLogs,
                'this_week' => $weekLogs
            ],
            'event_types' => $eventTypes,
            'model_types' => $modelTypes,
            'top_users' => $topUsers
        ];
    }

    /**
     * Export audit data to CSV
     */
    public function exportAuditData(array $filters = []): string
    {
        $query = AuditLog::query()->with(['user', 'branch']);
        $this->applyAuditFilters($query, $filters);

        // Apply branch scope for branch admins
        if (auth()->user() && auth()->user()->role === 'branch_admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'audit_logs_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // Write header
        fputcsv($file, [
            'Date/Time',
            'Event Type',
            'Model Type',
            'Model ID',
            'User',
            'Branch',
            'Changed Fields',
            'IP Address',
            'URL'
        ]);

        // Write data
        foreach ($logs as $log) {
            fputcsv($file, [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->event_type,
                $log->getModelName(),
                $log->auditable_id,
                $log->user?->name ?? 'System',
                $log->branch?->name ?? 'N/A',
                implode(', ', $log->changed_fields ?? []),
                $log->ip_address,
                $log->url
            ]);
        }

        fclose($file);

        return 'exports/' . $filename;
    }

    /**
     * Clean up old audit logs based on retention policy
     */
    public function cleanupOldLogs(int $retentionDays = 730): int // 2 years default
    {
        $cutoffDate = now()->subDays($retentionDays);
        
        $deletedAuditLogs = AuditLog::where('created_at', '<', $cutoffDate)->delete();
        $deletedActivityLogs = UserActivityLog::where('created_at', '<', $cutoffDate)->delete();

        return $deletedAuditLogs + $deletedActivityLogs;
    }

    /**
     * Get data retention status
     */
    public function getDataRetentionStatus(): array
    {
        $retentionDays = 730; // 2 years
        $cutoffDate = now()->subDays($retentionDays);

        $oldAuditLogs = AuditLog::where('created_at', '<', $cutoffDate)->count();
        $oldActivityLogs = UserActivityLog::where('created_at', '<', $cutoffDate)->count();
        $totalOldLogs = $oldAuditLogs + $oldActivityLogs;

        $totalLogs = AuditLog::count() + UserActivityLog::count();
        $currentLogs = $totalLogs - $totalOldLogs;

        return [
            'retention_days' => $retentionDays,
            'cutoff_date' => $cutoffDate->format('Y-m-d'),
            'current_logs' => $currentLogs,
            'old_logs' => $totalOldLogs,
            'total_logs' => $totalLogs,
            'retention_percentage' => $totalLogs > 0 ? round(($currentLogs / $totalLogs) * 100, 2) : 100
        ];
    }

    /**
     * Get retention status (alias for getDataRetentionStatus)
     */
    public function getRetentionStatus(): array
    {
        return $this->getDataRetentionStatus();
    }

    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================

    /**
     * Apply filters to audit query
     */
    private function applyAuditFilters($query, array $filters): void
    {
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->orWhere('auditable_type', 'LIKE', "%{$search}%")
                ->orWhere('event_type', 'LIKE', "%{$search}%")
                ->orWhere('url', 'LIKE', "%{$search}%")
                ->orWhere('ip_address', 'LIKE', "%{$search}%");
            });
        }

        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from']));
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        // User filter
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Event type filter
        if (!empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        // Model type filter
        if (!empty($filters['model_type'])) {
            $query->where('auditable_type', 'LIKE', "%{$filters['model_type']}%");
        }

        // Branch filter (for company admins)
        if (!empty($filters['branch_id']) && auth()->user()?->role === 'company_admin') {
            $query->where('branch_id', $filters['branch_id']);
        }
    }

    /**
     * Protect sensitive data in audit logs
     */
    private function protectSensitiveData(array $data): array
    {
        $sensitiveFields = [
            'password', 'password_hash', 'remember_token',
            'api_key', 'api_token', 'secret_key',
            'credit_card', 'card_number', 'cvv',
            'ssn', 'social_security', 'tax_id'
        ];

        foreach ($sensitiveFields as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = '[PROTECTED]';
            }
        }

        return $data;
    }

    /**
     * Generate activity description based on type and context
     */
    private function generateActivityDescription(string $activity, array $context): string
    {
        $descriptions = [
            'login' => 'User successfully logged in',
            'logout' => 'User logged out',
            'failed_login' => 'Failed login attempt',
            'password_change' => 'User changed their password',
            'profile_update' => 'User updated their profile'
        ];

        $baseDescription = $descriptions[$activity] ?? "User performed {$activity}";

        // Add context if available
        if (!empty($context['details'])) {
            $baseDescription .= ' - ' . $context['details'];
        }

        return $baseDescription;
    }

    /**
     * Get users list for filter dropdown
     */
    public function getUsersList(): array
    {
        return AuditLog::select('user_id')
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * Get event types for filter dropdown
     */
    public function getEventTypes(): array
    {
        return AuditLog::select('event_type')
            ->distinct()
            ->pluck('event_type')
            ->toArray();
    }

    /**
     * Get model types for filter dropdown
     */
    public function getModelTypes(): array
    {
        return AuditLog::select('auditable_type')
            ->distinct()
            ->pluck('auditable_type')
            ->toArray();
    }

    /**
     * Get branches list for filter dropdown
     */
    public function getBranchesList(): array
    {
        return AuditLog::select('branch_id')
            ->whereNotNull('branch_id')
            ->distinct()
            ->pluck('branch_id')
            ->toArray();
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10): Collection
    {
        return AuditLog::with(['user', 'branch'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }



    /**
     * Export audit logs
     */
    public function exportAuditLogs(array $filters = [], string $format = 'csv'): string
    {
        $logs = $this->getAuditLogs($filters, 1000); // Get more records for export

        $data = [];
        foreach ($logs as $log) {
            $data[] = [
                'ID' => $log->id,
                'Event Type' => $log->event_type,
                'Model Type' => $log->auditable_type,
                'Model ID' => $log->auditable_id,
                'User' => $log->user_name ?? 'Unknown',
                'Branch' => $log->branch_name ?? 'N/A',
                'IP Address' => $log->ip_address,
                'Created At' => $log->created_at->format('Y-m-d H:i:s')
            ];
        }

        return $this->generateExport($data, $format, 'audit_logs');
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
     * Get cutoff date based on retention period
     */
    private function getCutoffDate(string $olderThan): Carbon
    {
        return match($olderThan) {
            '1_year' => Carbon::now()->subYear(),
            '2_years' => Carbon::now()->subYears(2),
            '3_years' => Carbon::now()->subYears(3),
            '6_months' => Carbon::now()->subMonths(6),
            default => Carbon::now()->subYears(2)
        };
    }

    /**
     * Generate export file
     */
    private function generateExport(array $data, string $format, string $filename): string
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