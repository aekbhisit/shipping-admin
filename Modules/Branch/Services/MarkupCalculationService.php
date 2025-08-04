<?php

namespace Modules\Branch\Services;

use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchMarkup;

/**
 * MarkupCalculationService
 * Purpose: Service layer for markup calculation (real-time calculation at quote time)
 */
class MarkupCalculationService
{
    /**
     * Calculate markup for given branch, carrier, and base price
     */
    public function calculateMarkup(int $branchId, int $carrierId, float $basePrice): float
    {
        $markupRule = $this->getMarkupPercentage($branchId, $carrierId);
        
        if (!$markupRule) {
            return $basePrice; // No markup rule found, return base price
        }

        return $markupRule->calculateMarkup($basePrice);
    }

    /**
     * Get markup percentage for branch and carrier
     */
    public function getMarkupPercentage(int $branchId, int $carrierId): ?BranchMarkup
    {
        return BranchMarkup::where('branch_id', $branchId)
            ->where('carrier_id', $carrierId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Validate markup rules before applying
     */
    public function validateMarkupRules(int $branchId, int $carrierId, float $percentage): bool
    {
        $markupRule = $this->getMarkupPercentage($branchId, $carrierId);
        
        if (!$markupRule) {
            // If no rule exists, check basic validation
            return $percentage >= 0 && $percentage <= 100;
        }

        return $markupRule->isWithinLimits($percentage);
    }

    /**
     * Calculate markup amount only (without base price)
     */
    public function getMarkupAmount(int $branchId, int $carrierId, float $basePrice): float
    {
        $finalPrice = $this->calculateMarkup($branchId, $carrierId, $basePrice);
        return $finalPrice - $basePrice;
    }

    /**
     * Calculate markup for multiple items/quotes
     */
    public function calculateBulkMarkup(int $branchId, array $quotes): array
    {
        $results = [];
        
        foreach ($quotes as $quote) {
            $carrierId = $quote['carrier_id'];
            $basePrice = $quote['base_price'];
            
            $finalPrice = $this->calculateMarkup($branchId, $carrierId, $basePrice);
            $markupAmount = $finalPrice - $basePrice;
            
            $results[] = [
                'quote_id' => $quote['id'] ?? null,
                'carrier_id' => $carrierId,
                'base_price' => $basePrice,
                'markup_amount' => $markupAmount,
                'final_price' => $finalPrice,
                'markup_percentage' => $basePrice > 0 ? ($markupAmount / $basePrice) * 100 : 0
            ];
        }
        
        return $results;
    }

    /**
     * Get all markup rules for a branch
     */
    public function getBranchMarkupRules(int $branchId): array
    {
        $markups = BranchMarkup::where('branch_id', $branchId)
            ->with('carrier')
            ->active()
            ->get();

        return $markups->map(function ($markup) {
            return [
                'carrier_id' => $markup->carrier_id,
                'carrier_name' => $markup->carrier->name ?? 'Unknown',
                'markup_percentage' => $markup->markup_percentage,
                'min_markup_amount' => $markup->min_markup_amount,
                'max_markup_percentage' => $markup->max_markup_percentage,
                'is_active' => $markup->is_active
            ];
        })->toArray();
    }

    /**
     * Calculate revenue projection for branch
     */
    public function calculateRevenueProjection(int $branchId, array $projectedShipments): array
    {
        $totalBaseRevenue = 0;
        $totalMarkupRevenue = 0;
        $carrierBreakdown = [];

        foreach ($projectedShipments as $shipment) {
            $carrierId = $shipment['carrier_id'];
            $basePrice = $shipment['base_price'];
            $quantity = $shipment['quantity'] ?? 1;

            $finalPrice = $this->calculateMarkup($branchId, $carrierId, $basePrice);
            $markupAmount = ($finalPrice - $basePrice) * $quantity;
            $baseRevenue = $basePrice * $quantity;

            $totalBaseRevenue += $baseRevenue;
            $totalMarkupRevenue += $markupAmount;

            // Track by carrier
            if (!isset($carrierBreakdown[$carrierId])) {
                $carrierBreakdown[$carrierId] = [
                    'base_revenue' => 0,
                    'markup_revenue' => 0,
                    'shipment_count' => 0
                ];
            }

            $carrierBreakdown[$carrierId]['base_revenue'] += $baseRevenue;
            $carrierBreakdown[$carrierId]['markup_revenue'] += $markupAmount;
            $carrierBreakdown[$carrierId]['shipment_count'] += $quantity;
        }

        return [
            'total_base_revenue' => $totalBaseRevenue,
            'total_markup_revenue' => $totalMarkupRevenue,
            'total_final_revenue' => $totalBaseRevenue + $totalMarkupRevenue,
            'markup_percentage' => $totalBaseRevenue > 0 ? ($totalMarkupRevenue / $totalBaseRevenue) * 100 : 0,
            'carrier_breakdown' => $carrierBreakdown
        ];
    }

    /**
     * Get markup efficiency for branch
     */
    public function getMarkupEfficiency(int $branchId, array $dateRange = []): array
    {
        $branch = Branch::findOrFail($branchId);
        $performance = $branch->getPerformanceMetrics($dateRange);

        $markupEfficiency = $performance['total_revenue'] > 0 
            ? ($performance['total_markup'] / $performance['total_revenue']) * 100
            : 0;

        // Get markup rules and their utilization
        $markupRules = $this->getBranchMarkupRules($branchId);
        
        // TODO: Update when Shipment module is implemented
        // Calculate utilization by checking actual shipments
        // $shipments = $branch->shipments()
        //     ->when(!empty($dateRange), function ($query) use ($dateRange) {
        //         $start = $dateRange['start'] ?? now()->subDays(30);
        //         $end = $dateRange['end'] ?? now();
        //         return $query->whereBetween('created_at', [$start, $end]);
        //     })
        //     ->get();

        $carrierUtilization = collect(); // TODO: Update when Shipment module is implemented

        return [
            'overall_markup_efficiency' => $markupEfficiency,
            'total_shipments' => $performance['total_shipments'],
            'total_revenue' => $performance['total_revenue'],
            'total_markup' => $performance['total_markup'],
            'carrier_utilization' => $carrierUtilization,
            'markup_rules_count' => count($markupRules),
            'active_carriers' => $carrierUtilization->count()
        ];
    }

    /**
     * Optimize markup rates based on historical data
     */
    public function suggestMarkupOptimization(int $branchId): array
    {
        $efficiency = $this->getMarkupEfficiency($branchId);
        $suggestions = [];

        foreach ($efficiency['carrier_utilization'] as $carrierId => $utilization) {
            $configuredRate = $utilization['configured_percentage'];
            $actualRate = $utilization['actual_percentage'];
            $shipmentCount = $utilization['shipment_count'];

            if ($shipmentCount < 10) {
                $suggestions[$carrierId] = [
                    'type' => 'insufficient_data',
                    'message' => 'Not enough shipments to analyze',
                    'suggested_action' => 'Continue monitoring'
                ];
                continue;
            }

            $difference = abs($configuredRate - $actualRate);
            
            if ($difference > 2) { // More than 2% difference
                if ($actualRate < $configuredRate) {
                    $suggestions[$carrierId] = [
                        'type' => 'underperforming',
                        'message' => 'Actual markup lower than configured',
                        'current_rate' => $configuredRate,
                        'actual_rate' => $actualRate,
                        'suggested_action' => 'Check minimum markup amount settings'
                    ];
                } else {
                    $suggestions[$carrierId] = [
                        'type' => 'overperforming',
                        'message' => 'Actual markup higher than configured',
                        'current_rate' => $configuredRate,
                        'actual_rate' => $actualRate,
                        'suggested_action' => 'Consider optimizing base rates'
                    ];
                }
            } else {
                $suggestions[$carrierId] = [
                    'type' => 'optimal',
                    'message' => 'Markup performing as expected',
                    'current_rate' => $configuredRate,
                    'actual_rate' => $actualRate,
                    'suggested_action' => 'No changes needed'
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Calculate competitive analysis
     */
    public function getCompetitiveAnalysis(int $branchId, array $competitorRates = []): array
    {
        $branchRules = $this->getBranchMarkupRules($branchId);
        $analysis = [];

        foreach ($branchRules as $rule) {
            $carrierId = $rule['carrier_id'];
            $ourRate = $rule['markup_percentage'];
            $competitorRate = $competitorRates[$carrierId] ?? null;

            if ($competitorRate !== null) {
                $difference = $ourRate - $competitorRate;
                $status = $difference > 0 ? 'higher' : ($difference < 0 ? 'lower' : 'equal');
                
                $analysis[$carrierId] = [
                    'carrier_name' => $rule['carrier_name'],
                    'our_rate' => $ourRate,
                    'competitor_rate' => $competitorRate,
                    'difference' => $difference,
                    'status' => $status,
                    'recommendation' => $this->getCompetitiveRecommendation($difference, $ourRate)
                ];
            } else {
                $analysis[$carrierId] = [
                    'carrier_name' => $rule['carrier_name'],
                    'our_rate' => $ourRate,
                    'competitor_rate' => null,
                    'difference' => null,
                    'status' => 'no_data',
                    'recommendation' => 'Gather competitor data for analysis'
                ];
            }
        }

        return $analysis;
    }

    /**
     * Get recommendation based on competitive analysis
     */
    private function getCompetitiveRecommendation(float $difference, float $ourRate): string
    {
        if (abs($difference) <= 1) {
            return 'Rates are competitive - maintain current pricing';
        } elseif ($difference > 5) {
            return 'Consider reducing markup rate to stay competitive';
        } elseif ($difference < -5) {
            return 'Opportunity to increase markup rate while staying competitive';
        } else {
            return 'Minor adjustment may be beneficial';
        }
    }

    /**
     * Validate markup configuration for branch
     */
    public function validateBranchMarkupConfiguration(int $branchId): array
    {
        $issues = [];
        $markupRules = BranchMarkup::where('branch_id', $branchId)->get();

        // Check for missing carrier rules
        $availableCarriers = \Modules\Shipper\Entities\Carrier::active()->pluck('id')->toArray();
        $configuredCarriers = $markupRules->pluck('carrier_id')->toArray();
        $missingCarriers = array_diff($availableCarriers, $configuredCarriers);

        if (!empty($missingCarriers)) {
            $issues[] = [
                'type' => 'missing_carriers',
                'message' => 'Some carriers do not have markup rules configured',
                'missing_carrier_ids' => $missingCarriers
            ];
        }

        // Check for invalid markup rules
        foreach ($markupRules as $rule) {
            if (!$rule->isValid()) {
                $issues[] = [
                    'type' => 'invalid_rule',
                    'message' => 'Invalid markup rule configuration',
                    'carrier_id' => $rule->carrier_id,
                    'rule_id' => $rule->id
                ];
            }

            if ($rule->markup_percentage > $rule->max_markup_percentage) {
                $issues[] = [
                    'type' => 'percentage_exceeds_max',
                    'message' => 'Markup percentage exceeds maximum allowed',
                    'carrier_id' => $rule->carrier_id,
                    'current' => $rule->markup_percentage,
                    'max_allowed' => $rule->max_markup_percentage
                ];
            }
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
            'total_rules' => $markupRules->count(),
            'active_rules' => $markupRules->where('is_active', true)->count()
        ];
    }
} 