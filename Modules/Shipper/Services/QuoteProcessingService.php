<?php

namespace Modules\Shipper\Services;

use Modules\Shipper\Entities\Carrier;
use Modules\Shipper\Entities\CarrierCredential;
use Illuminate\Support\Facades\Log;

/**
 * QuoteProcessingService
 * Purpose: Handle sequential quote processing from multiple carriers
 * Implementation: Sequential API calls with simple error handling
 */
class QuoteProcessingService
{
    protected $carrierApiService;

    public function __construct(CarrierApiService $carrierApiService)
    {
        $this->carrierApiService = $carrierApiService;
    }

    /**
     * Process quote request for multiple carriers
     * Strategy: Sequential API calls with simple error handling
     * Implementation: On-demand quote generation per carrier selection
     */
    public function processQuoteRequest(array $packageData, int $branchId): array
    {
        $quotes = [];
        
        try {
            // Get all active carriers that have credentials for this branch
            $carriers = $this->getAvailableCarriers($branchId);

            if (empty($carriers)) {
                Log::warning('No carriers available for branch', ['branch_id' => $branchId]);
                return [];
            }

            // Sequential processing of each carrier
            foreach ($carriers as $carrier) {
                try {
                    Log::info('Processing quote request for carrier', [
                        'carrier_id' => $carrier->id,
                        'carrier_name' => $carrier->name,
                        'branch_id' => $branchId
                    ]);

                    // Get quote from this carrier
                    $quote = $this->carrierApiService->getQuoteFromCarrier(
                        $carrier->id, 
                        $packageData, 
                        $branchId
                    );

                    if ($quote['success']) {
                        $quotes[] = $quote;
                        Log::info('Quote successful for carrier', [
                            'carrier_id' => $carrier->id,
                            'price' => $quote['quote_price']
                        ]);
                    } else {
                        Log::warning('Quote failed for carrier', [
                            'carrier_id' => $carrier->id,
                            'error' => $quote['error']
                        ]);
                        
                        // Continue with other carriers even if one fails
                        continue;
                    }

                    // Basic rate limiting - simple delay between carriers
                    if (count($carriers) > 1) {
                        usleep(500000); // 0.5 second delay between carriers
                    }

                } catch (\Exception $e) {
                    Log::error('Exception processing quote for carrier', [
                        'carrier_id' => $carrier->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Continue with other carriers
                    continue;
                }
            }

            // Sort quotes by price (lowest first) for simple presentation
            usort($quotes, function($a, $b) {
                return $a['quote_price'] <=> $b['quote_price'];
            });

            return $quotes;

        } catch (\Exception $e) {
            Log::error('Exception in quote processing', [
                'branch_id' => $branchId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    /**
     * Get single carrier quote
     * Used for specific carrier quote requests
     */
    public function getSingleCarrierQuote(int $carrierId, array $packageData, int $branchId): array
    {
        try {
            // Verify carrier has credentials for this branch
            $hasCredentials = CarrierCredential::where('branch_id', $branchId)
                ->where('carrier_id', $carrierId)
                ->where('is_active', true)
                ->exists();

            if (!$hasCredentials) {
                return [
                    'success' => false,
                    'error' => 'No active credentials found for this carrier'
                ];
            }

            return $this->carrierApiService->getQuoteFromCarrier($carrierId, $packageData, $branchId);

        } catch (\Exception $e) {
            Log::error('Exception getting single carrier quote', [
                'carrier_id' => $carrierId,
                'branch_id' => $branchId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to get quote: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get available carriers for branch
     * Returns carriers that have active credentials
     */
    protected function getAvailableCarriers(int $branchId): array
    {
        try {
            return Carrier::active()
                ->whereHas('carrierCredentials', function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->where('is_active', true);
                })
                ->orderBy('name')
                ->get()
                ->toArray();

        } catch (\Exception $e) {
            Log::error('Exception getting available carriers', [
                'branch_id' => $branchId,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Validate package data before processing
     * Validation: Simple range validation
     */
    public function validatePackageData(array $packageData): array
    {
        $errors = [];

        // Weight validation
        if (!isset($packageData['package']['weight']) || $packageData['package']['weight'] < 0.1) {
            $errors[] = 'Package weight must be at least 0.1 kg';
        }
        if (isset($packageData['package']['weight']) && $packageData['package']['weight'] > 30) {
            $errors[] = 'Package weight cannot exceed 30 kg';
        }

        // Dimension validation
        foreach (['length', 'width', 'height'] as $dimension) {
            if (!isset($packageData['package'][$dimension]) || $packageData['package'][$dimension] < 1) {
                $errors[] = "Package {$dimension} must be at least 1 cm";
            }
            if (isset($packageData['package'][$dimension]) && $packageData['package'][$dimension] > 100) {
                $errors[] = "Package {$dimension} cannot exceed 100 cm";
            }
        }

        // Postcode validation
        if (!isset($packageData['pickup']['postcode']) || !preg_match('/^\d{5}$/', $packageData['pickup']['postcode'])) {
            $errors[] = 'Pickup postcode must be 5 digits';
        }
        if (!isset($packageData['delivery']['postcode']) || !preg_match('/^\d{5}$/', $packageData['delivery']['postcode'])) {
            $errors[] = 'Delivery postcode must be 5 digits';
        }

        return $errors;
    }

    /**
     * Format quotes for simple list presentation
     * Format: Simple list format without comparison
     */
    public function formatQuotesForDisplay(array $quotes): array
    {
        return array_map(function($quote) {
            return [
                'carrier_id' => $quote['carrier_id'],
                'carrier_name' => $quote['carrier_name'],
                'service_type' => $quote['service_type'],
                'price' => '฿' . number_format($quote['quote_price'], 2),
                'price_numeric' => $quote['quote_price'],
                'estimated_days' => $quote['estimated_days'] ?? 'N/A',
                'processing_time' => $quote['processing_time_ms'] . 'ms'
            ];
        }, $quotes);
    }
} 