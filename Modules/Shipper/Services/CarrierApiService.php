<?php

namespace Modules\Shipper\Services;

use Modules\Shipper\Entities\Carrier;
use Modules\Shipper\Entities\CarrierCredential;
use Modules\Shipper\Entities\QuoteRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * CarrierApiService
 * Purpose: Central service for carrier API integration
 * Implementation: Sequential API calls with simple error handling
 */
class CarrierApiService
{
    protected $thailandPostService;
    protected $jtExpressService;
    protected $flashExpressService;

    public function __construct(
        ThailandPostApiService $thailandPostService,
        JTExpressApiService $jtExpressService,
        FlashExpressApiService $flashExpressService
    ) {
        $this->thailandPostService = $thailandPostService;
        $this->jtExpressService = $jtExpressService;
        $this->flashExpressService = $flashExpressService;
    }

    /**
     * Get quote from specific carrier
     * Implementation: Direct integration with quote selection
     * Error Handling: Basic error logging and user notification
     */
    public function getQuoteFromCarrier(int $carrierId, array $packageData, int $branchId): array
    {
        $startTime = microtime(true);
        
        try {
            // Get carrier information
            $carrier = Carrier::findOrFail($carrierId);
            
            // Get branch credentials for this carrier
            $credential = CarrierCredential::where('branch_id', $branchId)
                ->where('carrier_id', $carrierId)
                ->where('is_active', true)
                ->first();

            if (!$credential) {
                return [
                    'success' => false,
                    'error' => 'No active credentials found for this carrier',
                    'quote_price' => null
                ];
            }

            // Get carrier-specific service
            $apiService = $this->getCarrierService($carrier->code);
            
            if (!$apiService) {
                return [
                    'success' => false,
                    'error' => 'Carrier service not supported',
                    'quote_price' => null
                ];
            }

            // Make API call to carrier
            $quoteResult = $apiService->getQuote($credential->getDecryptedCredentials(), $packageData);
            
            $processingTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

            // Log the request for debugging
            $this->logQuoteRequest(
                $branchId, 
                $carrierId, 
                $packageData, 
                $quoteResult, 
                $processingTime
            );

            if ($quoteResult['success']) {
                // Apply branch markup to the base price
                $finalPrice = $this->applyBranchMarkup(
                    $quoteResult['quote_price'], 
                    $branchId, 
                    $carrierId
                );

                return [
                    'success' => true,
                    'carrier_id' => $carrierId,
                    'carrier_name' => $carrier->name,
                    'service_type' => $quoteResult['service_type'] ?? 'Standard',
                    'base_price' => $quoteResult['quote_price'],
                    'quote_price' => $finalPrice,
                    'estimated_days' => $quoteResult['estimated_days'] ?? null,
                    'processing_time_ms' => $processingTime
                ];
            }

            return [
                'success' => false,
                'error' => $quoteResult['error'] ?? 'Unknown error occurred',
                'quote_price' => null
            ];

        } catch (\Exception $e) {
            $processingTime = (microtime(true) - $startTime) * 1000;
            
            // Log error
            $this->logQuoteRequest(
                $branchId, 
                $carrierId, 
                $packageData, 
                ['success' => false, 'error' => $e->getMessage()], 
                $processingTime
            );

            Log::error('Carrier API Error', [
                'carrier_id' => $carrierId,
                'branch_id' => $branchId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'API connection failed: ' . $e->getMessage(),
                'quote_price' => null
            ];
        }
    }

    /**
     * Test carrier connection
     * Implementation: Basic availability check on API call
     */
    public function testCarrierConnection(int $carrierId, int $branchId): bool
    {
        try {
            // Use sample test data
            $testPackageData = [
                'package' => [
                    'weight' => 1.0,
                    'length' => 10,
                    'width' => 10,
                    'height' => 10
                ],
                'pickup' => [
                    'postcode' => '10110'
                ],
                'delivery' => [
                    'postcode' => '10120'
                ]
            ];

            $result = $this->getQuoteFromCarrier($carrierId, $testPackageData, $branchId);
            
            return $result['success'];

        } catch (\Exception $e) {
            Log::error('Carrier connection test failed', [
                'carrier_id' => $carrierId,
                'branch_id' => $branchId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Apply branch markup to base price
     * Strategy: Fixed percentage markup per branch per carrier
     */
    protected function applyBranchMarkup(float $basePrice, int $branchId, int $carrierId): float
    {
        try {
            // Get branch markup for this carrier
            $branchMarkup = \Modules\Branch\Entities\BranchMarkup::where('branch_id', $branchId)
                ->where('carrier_id', $carrierId)
                ->first();

            if ($branchMarkup && $branchMarkup->markup_percentage > 0) {
                $markupAmount = $basePrice * ($branchMarkup->markup_percentage / 100);
                return $basePrice + $markupAmount;
            }

            // Default markup if no specific markup found (5%)
            return $basePrice * 1.05;

        } catch (\Exception $e) {
            Log::error('Error applying branch markup', [
                'branch_id' => $branchId,
                'carrier_id' => $carrierId,
                'base_price' => $basePrice,
                'error' => $e->getMessage()
            ]);

            // Return original price if markup calculation fails
            return $basePrice;
        }
    }

    /**
     * Get carrier-specific API service
     */
    protected function getCarrierService(string $carrierCode)
    {
        switch (strtoupper($carrierCode)) {
            case 'TP':
                return $this->thailandPostService;
            case 'JT':
                return $this->jtExpressService;
            case 'FLASH':
                return $this->flashExpressService;
            default:
                return null;
        }
    }

    /**
     * Log quote request for debugging and analytics
     */
    protected function logQuoteRequest(
        int $branchId, 
        int $carrierId, 
        array $requestData, 
        array $responseData, 
        float $processingTimeMs
    ): void {
        try {
            QuoteRequest::create([
                'branch_id' => $branchId,
                'carrier_id' => $carrierId,
                'request_data' => $requestData,
                'response_data' => $responseData,
                'quote_price' => $responseData['quote_price'] ?? null,
                'service_type' => $responseData['service_type'] ?? null,
                'is_successful' => $responseData['success'] ?? false,
                'error_message' => $responseData['error'] ?? null,
                'processing_time_ms' => round($processingTimeMs),
                'requested_at' => now(),
                'requested_by' => auth()->id() ?? 1
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log quote request', [
                'error' => $e->getMessage(),
                'branch_id' => $branchId,
                'carrier_id' => $carrierId
            ]);
        }
    }
} 