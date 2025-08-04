<?php

namespace Modules\Branch\Services;

use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchMarkup;
use Modules\Branch\Services\MarkupCalculationService;

/**
 * BranchService
 * Purpose: Branch business logic and operations
 */
class BranchService
{
    protected $markupService;

    public function __construct(MarkupCalculationService $markupService)
    {
        $this->markupService = $markupService;
    }

    /**
     * Create new branch with validation
     */
    public function createBranch(array $data): Branch
    {
        // Validate required fields
        $this->validateBranchData($data);

        // Auto-generate code if not provided
        if (empty($data['code'])) {
            $data['code'] = Branch::generateBranchCode($data['name']);
        }

        // Create the branch
        $branch = Branch::create($data);

        // Log branch creation
        \Log::info('Branch created', [
            'branch_id' => $branch->id,
            'branch_code' => $branch->code,
            'created_by' => $data['created_by'] ?? null
        ]);

        return $branch;
    }

    /**
     * Update branch settings
     */
    public function updateBranchSettings(int $branchId, array $settings): bool
    {
        $branch = Branch::findOrFail($branchId);
        
        // Merge new settings with existing ones
        $currentSettings = $branch->settings ?? [];
        $updatedSettings = array_merge($currentSettings, $settings);

        return $branch->update(['settings' => $updatedSettings]);
    }

    /**
     * Calculate markup for quote using real-time calculation
     */
    public function calculateMarkupForQuote(int $branchId, int $carrierId, float $basePrice): float
    {
        return $this->markupService->calculateMarkup($branchId, $carrierId, $basePrice);
    }

    /**
     * Generate performance report for branch
     */
    public function generatePerformanceReport(int $branchId, array $dateRange = []): array
    {
        $branch = Branch::findOrFail($branchId);
        
        $startDate = $dateRange['start'] ?? now()->subDays(30);
        $endDate = $dateRange['end'] ?? now();

        // Get basic metrics
        $performance = $branch->getPerformanceMetrics(['start' => $startDate, 'end' => $endDate]);
        
        // TODO: Update when Shipment module is implemented
        // Get detailed shipment data
        // $shipments = $branch->shipments()
        //     ->whereBetween('created_at', [$startDate, $endDate])
        //     ->with('carrier')
        //     ->get();

        // Calculate additional metrics
        $carrierBreakdown = collect(); // TODO: Update when Shipment module is implemented

        // Daily breakdown
        $dailyBreakdown = collect(); // TODO: Update when Shipment module is implemented

        return [
            'branch' => $branch,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1
            ],
            'summary' => $performance,
            'carrier_breakdown' => $carrierBreakdown,
            'daily_breakdown' => $dailyBreakdown,
            'trends' => $this->calculateTrends($branch, $dateRange)
        ];
    }

    /**
     * Validate markup limits according to business rules
     */
    public function validateMarkupLimits(float $percentage): bool
    {
        // Minimum and maximum markup percentage limits
        $minMarkup = 0.0;
        $maxMarkup = 100.0;

        return $percentage >= $minMarkup && $percentage <= $maxMarkup;
    }

    /**
     * Get branch performance comparison
     */
    public function getBranchComparison(array $branchIds, array $dateRange = []): array
    {
        $branches = Branch::whereIn('id', $branchIds)->get();
        
        $comparison = [];
        
        foreach ($branches as $branch) {
            $performance = $branch->getPerformanceMetrics($dateRange);
            
            $comparison[] = [
                'branch' => $branch,
                'performance' => $performance,
                'markup_efficiency' => $performance['total_revenue'] > 0 
                    ? ($performance['total_markup'] / $performance['total_revenue']) * 100 
                    : 0
            ];
        }

        // Sort by total revenue
        usort($comparison, function ($a, $b) {
            return $b['performance']['total_revenue'] <=> $a['performance']['total_revenue'];
        });

        return $comparison;
    }

    /**
     * Setup default markups for new branch
     */
    public function setupDefaultMarkups(Branch $branch, array $carrierMarkups = []): void
    {
        $defaultMarkups = $carrierMarkups ?: $this->getDefaultMarkupRates();
        
        foreach ($defaultMarkups as $carrierId => $markupData) {
            BranchMarkup::create([
                'branch_id' => $branch->id,
                'carrier_id' => $carrierId,
                'markup_percentage' => $markupData['percentage'] ?? 10.0,
                'min_markup_amount' => $markupData['min_amount'] ?? 0.0,
                'max_markup_percentage' => $markupData['max_percentage'] ?? 50.0,
                'is_active' => true,
                'updated_by' => auth()->id()
            ]);
        }
    }

    /**
     * Calculate branch efficiency metrics
     */
    public function calculateBranchEfficiency(int $branchId, array $dateRange = []): array
    {
        $branch = Branch::findOrFail($branchId);
        $performance = $branch->getPerformanceMetrics($dateRange);
        
        $stats = $branch->getStats();
        
        // Calculate efficiency metrics
        $shipmentPerUser = $stats['active_users'] > 0 
            ? $performance['total_shipments'] / $stats['active_users']
            : 0;
            
        $revenuePerUser = $stats['active_users'] > 0 
            ? $performance['total_revenue'] / $stats['active_users']
            : 0;
            
        $markupEfficiency = $performance['total_revenue'] > 0 
            ? ($performance['total_markup'] / $performance['total_revenue']) * 100
            : 0;

        return [
            'shipments_per_user' => round($shipmentPerUser, 2),
            'revenue_per_user' => round($revenuePerUser, 2),
            'markup_efficiency' => round($markupEfficiency, 2),
            'average_shipment_value' => $performance['average_daily_revenue'] ?? 0,
            'days_active' => $performance['days_reported'] ?? 0,
            'total_users' => $stats['active_users']
        ];
    }

    /**
     * Get branch ranking based on performance
     */
    public function getBranchRanking(array $criteria = ['revenue']): array
    {
        $branches = Branch::active()->get();
        
        $rankings = $branches->map(function ($branch) use ($criteria) {
            $performance = $branch->getPerformanceMetrics();
            $score = 0;
            
            foreach ($criteria as $criterion) {
                switch ($criterion) {
                    case 'revenue':
                        $score += $performance['total_revenue'] ?? 0;
                        break;
                    case 'shipments':
                        $score += ($performance['total_shipments'] ?? 0) * 100; // Weight shipments
                        break;
                    case 'markup':
                        $score += $performance['total_markup'] ?? 0;
                        break;
                    case 'efficiency':
                        $efficiency = $this->calculateBranchEfficiency($branch->id);
                        $score += $efficiency['markup_efficiency'] * 10; // Weight efficiency
                        break;
                }
            }
            
            return [
                'branch' => $branch,
                'score' => $score,
                'performance' => $performance
            ];
        });

        // Sort by score descending
        $sorted = $rankings->sortByDesc('score')->values();
        
        // Add ranking position
        return $sorted->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });
    }

    /**
     * Validate branch data
     */
    private function validateBranchData(array $data): void
    {
        $required = ['name', 'address', 'phone', 'email', 'contact_person'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Field {$field} is required");
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }

        // Validate phone format (basic validation)
        if (!preg_match('/^[0-9+\-\s()]+$/', $data['phone'])) {
            throw new \InvalidArgumentException("Invalid phone format");
        }

        // Validate branch code uniqueness if provided
        if (!empty($data['code'])) {
            $existingBranch = Branch::where('code', $data['code'])->first();
            if ($existingBranch) {
                throw new \InvalidArgumentException("Branch code already exists");
            }
        }
    }

    /**
     * Calculate trends for branch performance
     */
    private function calculateTrends(Branch $branch, array $dateRange): array
    {
        $currentPeriod = $branch->getPerformanceMetrics($dateRange);
        
        // Calculate previous period for comparison
        $days = now()->diffInDays($dateRange['start'] ?? now()->subDays(30));
        $previousStart = ($dateRange['start'] ?? now()->subDays(30))->copy()->subDays($days);
        $previousEnd = ($dateRange['start'] ?? now()->subDays(30))->copy()->subDay();
        
        $previousPeriod = $branch->getPerformanceMetrics([
            'start' => $previousStart,
            'end' => $previousEnd
        ]);

        return [
            'shipments_trend' => $this->calculatePercentageChange(
                $previousPeriod['total_shipments'], 
                $currentPeriod['total_shipments']
            ),
            'revenue_trend' => $this->calculatePercentageChange(
                $previousPeriod['total_revenue'], 
                $currentPeriod['total_revenue']
            ),
            'markup_trend' => $this->calculatePercentageChange(
                $previousPeriod['total_markup'], 
                $currentPeriod['total_markup']
            )
        ];
    }

    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange(float $old, float $new): float
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }

        return (($new - $old) / $old) * 100;
    }

    /**
     * Get default markup rates for carriers
     */
    private function getDefaultMarkupRates(): array
    {
        return [
            // These would typically come from configuration or admin settings
            1 => ['percentage' => 10.0, 'min_amount' => 5.0, 'max_percentage' => 50.0], // Thailand Post
            2 => ['percentage' => 15.0, 'min_amount' => 10.0, 'max_percentage' => 60.0], // J&T Express
            3 => ['percentage' => 12.0, 'min_amount' => 8.0, 'max_percentage' => 55.0], // Flash Express
        ];
    }

    /**
     * Export branch data
     */
    public function exportBranchData(array $branchIds, string $format = 'csv'): array
    {
        $branches = Branch::whereIn('id', $branchIds)
            ->with(['creator', 'markups.carrier', 'reports'])
            ->get();

        $exportData = [];

        foreach ($branches as $branch) {
            $performance = $branch->getPerformanceMetrics();
            $stats = $branch->getStats();
            
            $exportData[] = [
                'branch_id' => $branch->id,
                'name' => $branch->name,
                'code' => $branch->code,
                'address' => $branch->address,
                'phone' => $branch->phone,
                'email' => $branch->email,
                'contact_person' => $branch->contact_person,
                'status' => $branch->is_active ? 'Active' : 'Inactive',
                'total_users' => $stats['total_users'],
                'active_users' => $stats['active_users'],
                'total_shipments' => $performance['total_shipments'],
                'total_revenue' => $performance['total_revenue'],
                'total_markup' => $performance['total_markup'],
                'created_at' => $branch->created_at->format('Y-m-d H:i:s'),
                'created_by' => $branch->creator?->name ?? 'Unknown'
            ];
        }

        return $exportData;
    }
} 