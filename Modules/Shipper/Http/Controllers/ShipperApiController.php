<?php

namespace Modules\Shipper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Shipper\Services\CarrierApiService;
use Modules\Shipper\Services\QuoteProcessingService;
use Modules\Shipper\Services\LabelGenerationService;
use Modules\Shipper\Services\TrackingService;
use Modules\Shipper\Services\PickupService;
use Modules\Shipper\Services\WebhookService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * ShipperApiController
 * Purpose: Handle API calls and responses for internal module integration
 * Access Level: Internal API for other modules
 */
class ShipperApiController extends Controller
{
    protected $carrierApiService;
    protected $quoteProcessingService;
    protected $labelGenerationService;
    protected $trackingService;
    protected $pickupService;
    protected $webhookService;

    public function __construct(
        CarrierApiService $carrierApiService,
        QuoteProcessingService $quoteProcessingService,
        LabelGenerationService $labelGenerationService,
        TrackingService $trackingService,
        PickupService $pickupService,
        WebhookService $webhookService
    ) {
        $this->carrierApiService = $carrierApiService;
        $this->quoteProcessingService = $quoteProcessingService;
        $this->labelGenerationService = $labelGenerationService;
        $this->trackingService = $trackingService;
        $this->pickupService = $pickupService;
        $this->webhookService = $webhookService;
    }

    /**
     * Get quotes from all carriers
     * Implementation: Parallel API calls with caching
     * Error Handling: Fallback to cached rates when API fails
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
            'service_type' => 'nullable|string',
            'branch_id' => 'required|integer|exists:branches,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $packageData = $request->all();
            $branchId = $request->get('branch_id');

            // Get quotes from all active carriers
            $quotes = $this->quoteProcessingService->getQuotesFromAllCarriers($packageData, $branchId);

            return response()->json([
                'success' => true,
                'quotes' => $quotes,
                'message' => count($quotes) . ' quotes retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Quote API Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get quotes: ' . $e->getMessage(),
                'quotes' => []
            ], 500);
        }
    }

    /**
     * Create shipment with selected carrier
     * Implementation: Direct carrier API integration
     * Features: Label generation, tracking number assignment
     */
    public function createShipment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'carrier_id' => 'required|integer|exists:carriers,id',
            'shipment_data' => 'required|array',
            'quote_id' => 'required|integer|exists:shipment_quotes,id',
            'branch_id' => 'required|integer|exists:branches,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shipment data',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $carrierId = $request->get('carrier_id');
            $shipmentData = $request->get('shipment_data');
            $quoteId = $request->get('quote_id');
            $branchId = $request->get('branch_id');

            // Create shipment through carrier API
            $shipment = $this->carrierApiService->createShipment($carrierId, $shipmentData, $quoteId, $branchId);

            return response()->json([
                'success' => true,
                'shipment' => $shipment,
                'message' => 'Shipment created successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Shipment Creation Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate shipping label
     * Implementation: Carrier-specific label generation
     * Features: PDF generation, base64 encoding for storage
     */
    public function generateLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_id' => 'required|integer|exists:shipments,id',
            'carrier_id' => 'required|integer|exists:carriers,id',
            'label_format' => 'nullable|string|in:PDF,ZPL,EPL'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid label request',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $shipmentId = $request->get('shipment_id');
            $carrierId = $request->get('carrier_id');
            $labelFormat = $request->get('label_format', 'PDF');

            // Generate label through carrier API
            $label = $this->labelGenerationService->generateLabel($shipmentId, $carrierId, $labelFormat);

            return response()->json([
                'success' => true,
                'label' => $label,
                'message' => 'Label generated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Label Generation Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate label: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tracking information
     * Implementation: Real-time tracking from carrier APIs
     * Features: Status updates, delivery confirmation
     */
    public function trackShipment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tracking_number' => 'required|string',
            'carrier_id' => 'required|integer|exists:carriers,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid tracking request',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $trackingNumber = $request->get('tracking_number');
            $carrierId = $request->get('carrier_id');

            // Get tracking information from carrier API
            $tracking = $this->trackingService->getTrackingInfo($trackingNumber, $carrierId);

            return response()->json([
                'success' => true,
                'tracking' => $tracking,
                'message' => 'Tracking information retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Tracking Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get tracking information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule pickup with carrier
     * Implementation: Carrier pickup scheduling API
     * Features: Pickup confirmation, time slot selection
     */
    public function schedulePickup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'carrier_id' => 'required|integer|exists:carriers,id',
            'pickup_date' => 'required|date|after:today',
            'pickup_time' => 'required|string',
            'pickup_address' => 'required|array',
            'shipment_ids' => 'required|array',
            'branch_id' => 'required|integer|exists:branches,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid pickup request',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $carrierId = $request->get('carrier_id');
            $pickupData = $request->all();

            // Schedule pickup through carrier API
            $pickup = $this->pickupService->schedulePickup($carrierId, $pickupData);

            return response()->json([
                'success' => true,
                'pickup' => $pickup,
                'message' => 'Pickup scheduled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Pickup Scheduling Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule pickup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process carrier webhooks
     * Implementation: Webhook endpoint for carrier status updates
     * Features: Status synchronization, automatic updates
     */
    public function handleWebhook(Request $request)
    {
        try {
            $webhookData = $request->all();
            $carrierId = $request->header('X-Carrier-ID');
            $signature = $request->header('X-Webhook-Signature');

            // Verify webhook signature
            if (!$this->webhookService->verifySignature($webhookData, $signature, $carrierId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid webhook signature'
                ], 401);
            }

            // Process webhook data
            $result = $this->webhookService->processWebhook($webhookData, $carrierId);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Webhook Processing Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'headers' => $request->headers->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook: ' . $e->getMessage()
            ], 500);
        }
    }
} 