<?php

namespace Modules\Shipper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Shipper\Services\CarrierApiService;
use Modules\Shipper\Services\QuoteProcessingService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * QuoteApiController
 * Purpose: Handle quote requests from shipment module
 * Access Level: Branch Staff
 */
class QuoteApiController extends Controller
{
    protected $carrierApiService;
    protected $quoteProcessingService;

    public function __construct(
        CarrierApiService $carrierApiService,
        QuoteProcessingService $quoteProcessingService
    ) {
        $this->carrierApiService = $carrierApiService;
        $this->quoteProcessingService = $quoteProcessingService;
        $this->middleware(['auth', 'role:branch_staff']);
    }

    /**
     * Get quotes from multiple carriers
     * Implementation: Sequential API calls with simple error handling
     * Timing: On-demand quote generation per carrier selection
     * Error Handling: Basic error logging and user notification
     */
    public function getQuotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package.weight' => 'required|numeric|min:0.1',
            'package.length' => 'required|numeric|min:1',
            'package.width' => 'required|numeric|min:1',
            'package.height' => 'required|numeric|min:1',
            'pickup.postcode' => 'required|string|size:5',
            'delivery.postcode' => 'required|string|size:5',
            'service_type' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid package data',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $branchId = Auth::user()->branch_id;
            $packageData = $request->all();

            // Process quote request using sequential API calls
            $quotes = $this->quoteProcessingService->processQuoteRequest($packageData, $branchId);

            return response()->json([
                'success' => true,
                'quotes' => $quotes,
                'message' => count($quotes) . ' quotes retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get quotes: ' . $e->getMessage(),
                'quotes' => []
            ], 500);
        }
    }

    /**
     * Get quote from specific carrier
     * Implementation: Direct API integration
     * Validation: Simple range validation for package dimensions
     */
    public function getCarrierQuote(Request $request, int $carrierId)
    {
        $validator = Validator::make($request->all(), [
            'package.weight' => 'required|numeric|min:0.1|max:30',
            'package.length' => 'required|numeric|min:1|max:100',
            'package.width' => 'required|numeric|min:1|max:100',
            'package.height' => 'required|numeric|min:1|max:100',
            'pickup.postcode' => 'required|string|size:5',
            'delivery.postcode' => 'required|string|size:5',
            'service_type' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid package data',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $branchId = Auth::user()->branch_id;
            $packageData = $request->all();

            // Get quote from specific carrier
            $quote = $this->carrierApiService->getQuoteFromCarrier($carrierId, $packageData, $branchId);

            if ($quote['success']) {
                return response()->json([
                    'success' => true,
                    'quote' => $quote,
                    'message' => 'Quote retrieved successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $quote['error'] ?? 'Failed to get quote',
                    'quote' => null
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get quote: ' . $e->getMessage(),
                'quote' => null
            ], 500);
        }
    }

    /**
     * Refresh quote for carrier
     * Caching: No caching - real-time calls only
     * Rate Limiting: Basic rate limiting with simple delays
     */
    public function refreshQuote(Request $request, int $carrierId)
    {
        // Basic rate limiting - simple delay for refresh requests
        sleep(1);

        return $this->getCarrierQuote($request, $carrierId);
    }

    /**
     * Get available carriers for branch
     * Returns list of carriers that have active credentials for the branch
     */
    public function getAvailableCarriers(Request $request)
    {
        try {
            $branchId = Auth::user()->branch_id;
            
            // Get carriers with active credentials for this branch
            $carriers = \Modules\Shipper\Entities\Carrier::active()
                ->whereHas('carrierCredentials', function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->where('is_active', true);
                })
                ->with(['carrierCredentials' => function($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                          ->where('is_active', true);
                }])
                ->get();

            return response()->json([
                'success' => true,
                'carriers' => $carriers->map(function($carrier) {
                    return [
                        'id' => $carrier->id,
                        'name' => $carrier->name,
                        'code' => $carrier->code,
                        'logo_url' => $carrier->logo_url,
                        'supported_services' => $carrier->getSupportedServices()
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get available carriers',
                'carriers' => []
            ], 500);
        }
    }

    /**
     * Get quote history for debugging
     * Returns recent quote requests for the branch
     */
    public function getQuoteHistory(Request $request)
    {
        try {
            $branchId = Auth::user()->branch_id;
            
            $quotes = \Modules\Shipper\Entities\QuoteRequest::byBranch($branchId)
                ->with(['carrier', 'requestedBy'])
                ->latest('requested_at')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'quotes' => $quotes->map(function($quote) {
                    return [
                        'id' => $quote->id,
                        'carrier_name' => $quote->carrier->name,
                        'service_type' => $quote->service_type,
                        'quote_price' => $quote->getFormattedPrice(),
                        'is_successful' => $quote->is_successful,
                        'processing_time' => $quote->formatted_processing_time,
                        'requested_at' => $quote->requested_at->format('Y-m-d H:i:s'),
                        'requested_by' => $quote->requestedBy->name ?? 'Unknown'
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get quote history',
                'quotes' => []
            ], 500);
        }
    }
} 