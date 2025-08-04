<?php

namespace Modules\Branch\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * BranchReport Model
 * Purpose: Basic performance analytics per branch (shipment count and revenue)
 */
class BranchReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'report_date',
        'shipment_count',
        'total_revenue',
        'total_markup',
        'report_data'
    ];

    protected $casts = [
        'report_date' => 'date',
        'shipment_count' => 'integer',
        'total_revenue' => 'decimal:2',
        'total_markup' => 'decimal:2',
        'report_data' => 'array'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Report belongs to a branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Generate daily report for branch
     */
    public static function generateDailyReport(int $branchId, \DateTime $date = null): array
    {
        if (!$date) {
            $date = now();
        }

        $dateString = $date->format('Y-m-d');

        // Get shipments for this branch and date
        $shipments = \DB::table('shipments')
            ->where('branch_id', $branchId)
            ->whereDate('created_at', $dateString)
            ->get();

        $shipmentCount = $shipments->count();
        $totalRevenue = $shipments->sum('total_amount');
        $totalMarkup = $shipments->sum('markup_amount');

        // Get additional metrics
        $carrierBreakdown = $shipments->groupBy('carrier_id')->map(function($items) {
            return [
                'count' => $items->count(),
                'revenue' => $items->sum('total_amount'),
                'markup' => $items->sum('markup_amount')
            ];
        });

        $reportData = [
            'carrier_breakdown' => $carrierBreakdown,
            'average_shipment_value' => $shipmentCount > 0 ? $totalRevenue / $shipmentCount : 0,
            'markup_percentage' => $totalRevenue > 0 ? ($totalMarkup / $totalRevenue) * 100 : 0,
            'generated_at' => now()
        ];

        return [
            'branch_id' => $branchId,
            'report_date' => $dateString,
            'shipment_count' => $shipmentCount,
            'total_revenue' => $totalRevenue,
            'total_markup' => $totalMarkup,
            'report_data' => $reportData
        ];
    }

    /**
     * Update metrics for the report
     */
    public function updateMetrics(array $data): void
    {
        $this->update([
            'shipment_count' => $data['shipment_count'] ?? $this->shipment_count,
            'total_revenue' => $data['total_revenue'] ?? $this->total_revenue,
            'total_markup' => $data['total_markup'] ?? $this->total_markup,
            'report_data' => array_merge($this->report_data ?? [], $data['report_data'] ?? [])
        ]);
    }

    /**
     * Get performance trend for period
     */
    public function getPerformanceTrend(): array
    {
        $previousReport = self::where('branch_id', $this->branch_id)
            ->where('report_date', '<', $this->report_date)
            ->orderBy('report_date', 'desc')
            ->first();

        if (!$previousReport) {
            return [
                'shipment_trend' => 0,
                'revenue_trend' => 0,
                'markup_trend' => 0
            ];
        }

        return [
            'shipment_trend' => $this->calculatePercentageChange($previousReport->shipment_count, $this->shipment_count),
            'revenue_trend' => $this->calculatePercentageChange($previousReport->total_revenue, $this->total_revenue),
            'markup_trend' => $this->calculatePercentageChange($previousReport->total_markup, $this->total_markup)
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
     * Get report summary
     */
    public function getSummary(): array
    {
        $averageShipmentValue = $this->shipment_count > 0 ? $this->total_revenue / $this->shipment_count : 0;
        $markupPercentage = $this->total_revenue > 0 ? ($this->total_markup / $this->total_revenue) * 100 : 0;

        return [
            'date' => $this->report_date->format('Y-m-d'),
            'shipments' => $this->shipment_count,
            'revenue' => $this->total_revenue,
            'markup' => $this->total_markup,
            'average_shipment_value' => $averageShipmentValue,
            'markup_percentage' => $markupPercentage,
            'performance_trend' => $this->getPerformanceTrend()
        ];
    }

    /**
     * Get carrier performance from report data
     */
    public function getCarrierPerformance(): array
    {
        return data_get($this->report_data, 'carrier_breakdown', []);
    }

    /**
     * Get top performing carrier
     */
    public function getTopCarrier(): ?array
    {
        $carriers = $this->getCarrierPerformance();
        
        if (empty($carriers)) {
            return null;
        }

        $topCarrier = collect($carriers)->sortByDesc('revenue')->first();
        
        return [
            'carrier_id' => array_search($topCarrier, $carriers),
            'performance' => $topCarrier
        ];
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope for specific branch
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }

    /**
     * Scope for current month
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('report_date', now()->month)
                    ->whereYear('report_date', now()->year);
    }

    /**
     * Scope for last month
     */
    public function scopeLastMonth($query)
    {
        $lastMonth = now()->subMonth();
        return $query->whereMonth('report_date', $lastMonth->month)
                    ->whereYear('report_date', $lastMonth->year);
    }

    /**
     * Scope for recent reports
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('report_date', '>=', now()->subDays($days));
    }

    /**
     * Scope for reports with revenue above threshold
     */
    public function scopeAboveRevenue($query, float $amount)
    {
        return $query->where('total_revenue', '>', $amount);
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get formatted revenue for display
     */
    public function getFormattedRevenueAttribute(): string
    {
        return '฿' . number_format($this->total_revenue, 2);
    }

    /**
     * Get formatted markup for display
     */
    public function getFormattedMarkupAttribute(): string
    {
        return '฿' . number_format($this->total_markup, 2);
    }

    /**
     * Get formatted date for display
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->report_date->format('M d, Y');
    }

    /**
     * Get average shipment value
     */
    public function getAverageShipmentValueAttribute(): float
    {
        return $this->shipment_count > 0 ? $this->total_revenue / $this->shipment_count : 0;
    }

    /**
     * Get markup percentage
     */
    public function getMarkupPercentageAttribute(): float
    {
        return $this->total_revenue > 0 ? ($this->total_markup / $this->total_revenue) * 100 : 0;
    }

    /**
     * Get formatted average shipment value
     */
    public function getFormattedAvgValueAttribute(): string
    {
        return '฿' . number_format($this->average_shipment_value, 2);
    }

    /**
     * Get formatted markup percentage
     */
    public function getFormattedMarkupPercentageAttribute(): string
    {
        return number_format($this->markup_percentage, 2) . '%';
    }

    /**
     * Get branch name for display
     */
    public function getBranchNameAttribute(): string
    {
        return $this->branch?->name ?? 'Unknown Branch';
    }

    /**
     * Get day of week for the report
     */
    public function getDayOfWeekAttribute(): string
    {
        return $this->report_date->format('l');
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

    /**
     * Create or update daily report
     */
    public static function createOrUpdateDaily(int $branchId, \DateTime $date = null): self
    {
        if (!$date) {
            $date = now();
        }

        $dateString = $date->format('Y-m-d');
        $reportData = self::generateDailyReport($branchId, $date);

        return self::updateOrCreate(
            [
                'branch_id' => $branchId,
                'report_date' => $dateString
            ],
            $reportData
        );
    }

    /**
     * Get summary for date range
     */
    public static function getSummaryForRange(int $branchId, $startDate, $endDate): array
    {
        $reports = self::forBranch($branchId)
            ->dateRange($startDate, $endDate)
            ->get();

        return [
            'total_shipments' => $reports->sum('shipment_count'),
            'total_revenue' => $reports->sum('total_revenue'),
            'total_markup' => $reports->sum('total_markup'),
            'average_daily_shipments' => $reports->avg('shipment_count'),
            'average_daily_revenue' => $reports->avg('total_revenue'),
            'days_reported' => $reports->count(),
            'best_day' => $reports->sortByDesc('total_revenue')->first(),
            'worst_day' => $reports->sortBy('total_revenue')->first()
        ];
    }

    /**
     * Get month-over-month comparison
     */
    public static function getMonthOverMonthComparison(int $branchId): array
    {
        $currentMonth = self::forBranch($branchId)->currentMonth()->get();
        $lastMonth = self::forBranch($branchId)->lastMonth()->get();

        $currentStats = [
            'shipments' => $currentMonth->sum('shipment_count'),
            'revenue' => $currentMonth->sum('total_revenue'),
            'markup' => $currentMonth->sum('total_markup')
        ];

        $lastStats = [
            'shipments' => $lastMonth->sum('shipment_count'),
            'revenue' => $lastMonth->sum('total_revenue'),
            'markup' => $lastMonth->sum('total_markup')
        ];

        return [
            'current_month' => $currentStats,
            'last_month' => $lastStats,
            'changes' => [
                'shipments' => self::calculateChange($lastStats['shipments'], $currentStats['shipments']),
                'revenue' => self::calculateChange($lastStats['revenue'], $currentStats['revenue']),
                'markup' => self::calculateChange($lastStats['markup'], $currentStats['markup'])
            ]
        ];
    }

    /**
     * Calculate percentage change (static version)
     */
    private static function calculateChange(float $old, float $new): float
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }

        return (($new - $old) / $old) * 100;
    }
} 