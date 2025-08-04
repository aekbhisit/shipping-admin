<?php

namespace Modules\Shipper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Shipper\Services\RateComparisonService;
use Modules\Shipper\Services\MarkupService;
use Modules\Shipper\Services\RateCacheService;
use Modules\Shipper\Services\PriceCalculationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * RateController
 * Purpose: Manage shipping rates and comparisons with branch markup
 * Access Level: Internal API for Shipment module
 */
class RateController extends Controller
{
    protected $rateComparisonService;
    protected $markupService;
    protected $rateCacheService;
    protected $priceCalculationService;

    public function __construct(
        RateComparisonService $rateComparisonService,
        MarkupService $markupService,
        RateCacheService $rateCacheService,
        PriceCalculationService $priceCalculationService
    ) {
        $this->rateComparisonService = $rateComparisonService;
        $this->markupService = $markupService;
        $this->rateCacheService = $rateCacheService;
        $this->priceCalculationService = $priceCalculationService;
    }

    /**
     * Compare rates from all carriers
     * Implementation: Parallel rate fetching with caching
     * Features: Rate comparison, service type filtering
     */
    public function compareRates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package.weight' => 'required|numeric|min:0.1',
            'package.length' => 'required|numeric|min:1',
            'package.width' => 'required|numeric|min:1',
            'package.height' => 'required|numeric|min:1',
            'pickup.postcode' => 'required|string|size:5',
            'delivery.postcode' => 'required|string|size:5',
            'service_type' => 'nullable|string',
            'branch_id' => 'required|integer|exists:branches,id',
            'include_inactive' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rate comparison request',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $packageData = $request->all();
            $branchId = $request->get('branch_id');
            $includeInactive = $request->get('include_inactive', false);

            // Compare rates from all carriers
            $comparison = $this->rateComparisonService->compareRates($packageData, $branchId, $includeInactive);

            return response()->json([
                'success' => true,
                'comparison' => $comparison,
                'message' => 'Rate comparison completed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Rate Comparison Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to compare rates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply branch markup rules
     * Implementation: Branch-specific markup calculation
     * Features: Percentage and fixed markup, service-specific rules
     */
    public function applyMarkup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'base_rate' => 'required|numeric|min:0',
            'carrier_id' => 'required|integer|exists:carriers,id',
            'branch_id' => 'required|integer|exists:branches,id',
            'service_type' => 'nullable|string',
            'package_weight' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid markup request',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $baseRate = $request->get('base_rate');
            $carrierId = $request->get('carrier_id');
            $branchId = $request->get('branch_id');
            $serviceType = $request->get('service_type');
            $packageWeight = $request->get('package_weight');

            // Apply branch markup to base rate
            $markedUpRate = $this->markupService->applyMarkup(
                $baseRate, 
                $carrierId, 
                $branchId, 
                $serviceType, 
                $packageWeight
            );

            return response()->json([
                'success' => true,
                'base_rate' => $baseRate,
                'marked_up_rate' => $markedUpRate,
                'markup_amount' => $markedUpRate - $baseRate,
                'markup_percentage' => (($markedUpRate - $baseRate) / $baseRate) * 100,
                'message' => 'Markup applied successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Markup Application Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to apply markup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cached rates when API fails
     * Implementation: Fallback to cached rates
     * Features: Cache validation, rate freshness checking
     */
    public function getCachedRates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package.weight' => 'required|numeric|min:0.1',
            'package.length' => 'required|numeric|min:1',
            'package.width' => 'required|numeric|min:1',
            'package.height' => 'required|numeric|min:1',
            'pickup.postcode' => 'required|string|size:5',
            'delivery.postcode' => 'required|string|size:5',
            'carrier_id' => 'nullable|integer|exists:carriers,id',
            'max_age_hours' => 'nullable|integer|min:1|max:168' // Max 1 week
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cached rates request',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $packageData = $request->all();
            $carrierId = $request->get('carrier_id');
            $maxAgeHours = $request->get('max_age_hours', 24); // Default 24 hours

            // Get cached rates
            $cachedRates = $this->rateCacheService->getCachedRates($packageData, $carrierId, $maxAgeHours);

            return response()->json([
                'success' => true,
                'cached_rates' => $cachedRates,
                'cache_age_hours' => $cachedRates['cache_age_hours'] ?? null,
                'message' => 'Cached rates retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Cached Rates Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get cached rates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh rate cache
     * Implementation: Force refresh of cached rates
     * Features: Background refresh, cache invalidation
     */
    public function refreshRates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'carrier_id' => 'nullable|integer|exists:carriers,id',
            'force_refresh' => 'nullable|boolean',
            'background' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid refresh request',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $carrierId = $request->get('carrier_id');
            $forceRefresh = $request->get('force_refresh', false);
            $background = $request->get('background', true);

            if ($background) {
                // Queue background refresh
                dispatch(function() use ($carrierId, $forceRefresh) {
                    $this->rateCacheService->refreshRates($carrierId, $forceRefresh);
                })->onQueue('rate-refresh');

                return response()->json([
                    'success' => true,
                    'message' => 'Rate refresh queued for background processing'
                ]);
            } else {
                // Immediate refresh
                $result = $this->rateCacheService->refreshRates($carrierId, $forceRefresh);

                return response()->json([
                    'success' => true,
                    'result' => $result,
                    'message' => 'Rate cache refreshed successfully'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Rate Refresh Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh rates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate final price with markup
     * Implementation: Complete price calculation including all fees
     * Features: Tax calculation, additional fees, discount application
     */
    public function calculateFinalPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'base_rate' => 'required|numeric|min:0',
            'carrier_id' => 'required|integer|exists:carriers,id',
            'branch_id' => 'required|integer|exists:branches,id',
            'service_type' => 'nullable|string',
            'package_weight' => 'nullable|numeric|min:0',
            'additional_fees' => 'nullable|array',
            'discount_code' => 'nullable|string',
            'customer_type' => 'nullable|string|in:retail,wholesale,premium'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid price calculation request',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $baseRate = $request->get('base_rate');
            $carrierId = $request->get('carrier_id');
            $branchId = $request->get('branch_id');
            $serviceType = $request->get('service_type');
            $packageWeight = $request->get('package_weight');
            $additionalFees = $request->get('additional_fees', []);
            $discountCode = $request->get('discount_code');
            $customerType = $request->get('customer_type', 'retail');

            // Calculate final price with all components
            $finalPrice = $this->priceCalculationService->calculateFinalPrice([
                'base_rate' => $baseRate,
                'carrier_id' => $carrierId,
                'branch_id' => $branchId,
                'service_type' => $serviceType,
                'package_weight' => $packageWeight,
                'additional_fees' => $additionalFees,
                'discount_code' => $discountCode,
                'customer_type' => $customerType
            ]);

            return response()->json([
                'success' => true,
                'price_breakdown' => $finalPrice,
                'final_price' => $finalPrice['total'],
                'message' => 'Final price calculated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Price Calculation Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate final price: ' . $e->getMessage()
            ], 500);
        }
    }
} 