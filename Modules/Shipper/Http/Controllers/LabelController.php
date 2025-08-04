<?php

namespace Modules\Shipper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Http\Controllers\AdminController;
use Modules\Shipper\Repositories\Contracts\LabelRepositoryInterface;
use Modules\Shipper\Repositories\Contracts\QuoteRepositoryInterface;

class LabelController extends AdminController
{
    protected $labelRepository;
    protected $quoteRepository;

    /**
     * Constructor - Inject Repositories
     */
    public function __construct(
        LabelRepositoryInterface $labelRepository,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->labelRepository = $labelRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Generate shipping label for specific carrier
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_id' => 'required|integer|exists:shipments,id',
            'carrier_id' => 'required|integer|exists:carriers,id',
            'quote_id' => 'required|integer|exists:shipment_quotes,id'
        ]);

        if ($validator->fails()) {
            $resp = ['success' => 0, 'code' => 301, 'msg' => 'Validation error', 'error' => $validator->errors()];
            return $this->resp($request, $resp);
        }

        $shipmentId = $request->get('shipment_id');
        $carrierId = $request->get('carrier_id');
        $quoteId = $request->get('quote_id');

        try {
            // Check if label already exists
            $existingLabel = $this->labelRepository->getLabelByShipment($shipmentId, $carrierId);
            if ($existingLabel) {
                $resp = [
                    'success' => 0, 
                    'code' => 409, 
                    'msg' => 'มีป้ายขนส่งสำหรับผู้ให้บริการนี้แล้ว',
                    'data' => ['label_id' => $existingLabel->id]
                ];
                return $this->resp($request, $resp);
            }

            // Get selected quote
            $quote = $this->quoteRepository->getSelectedQuote($shipmentId);
            if (!$quote || $quote->id != $quoteId) {
                $resp = ['success' => 0, 'code' => 400, 'msg' => 'กรุณาเลือกใบเสนอราคาก่อนสร้างป้ายขนส่ง'];
                return $this->resp($request, $resp);
            }

            // Generate label through API (placeholder implementation)
            $labelData = $this->generateCarrierLabel($quote);

            if ($labelData) {
                // Store label file
                $fileName = 'label_' . $shipmentId . '_' . $carrierId . '_' . time() . '.' . $labelData['format'];
                $filePath = 'shipping_labels/' . date('Y/m') . '/' . $fileName;
                
                Storage::disk('public')->put($filePath, $labelData['content']);

                // Save label record
                $label = $this->labelRepository->createLabel([
                    'shipment_id' => $shipmentId,
                    'carrier_id' => $carrierId,
                    'tracking_number' => $labelData['tracking_number'],
                    'label_format' => strtoupper($labelData['format']),
                    'label_path' => $filePath,
                    'label_data' => $labelData['base64_data'] ?? null,
                    'generated_at' => now()
                ]);

                $resp = [
                    'success' => 1, 
                    'code' => 200, 
                    'msg' => 'สร้างป้ายขนส่งสำเร็จ',
                    'data' => [
                        'label_id' => $label->id,
                        'tracking_number' => $labelData['tracking_number'],
                        'download_url' => route('shipper.labels.download', $label->id)
                    ]
                ];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'ไม่สามารถสร้างป้ายขนส่งได้'];
            }

        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Download label file
     */
    public function download($labelId)
    {
        $label = $this->labelRepository->find($labelId);
        
        if (!$label) {
            abort(404, 'Label not found');
        }

        if (!$label->fileExists()) {
            abort(404, 'Label file not found');
        }

        $fileName = 'label_' . $label->tracking_number . '.' . strtolower($label->label_format);
        
        return Storage::disk('public')->download($label->label_path, $fileName);
    }

    /**
     * Direct print label
     */
    public function print(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label_id' => 'required|integer|exists:shipping_labels,id'
        ]);

        if ($validator->fails()) {
            $resp = ['success' => 0, 'code' => 301, 'msg' => 'Invalid label ID'];
            return $this->resp($request, $resp);
        }

        $labelId = $request->get('label_id');
        $label = $this->labelRepository->find($labelId);

        if (!$label) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบป้ายขนส่ง'];
            return $this->resp($request, $resp);
        }

        if (!$label->fileExists()) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบไฟล์ป้ายขนส่ง'];
            return $this->resp($request, $resp);
        }

        try {
            // Return file URL for printing
            $resp = [
                'success' => 1, 
                'code' => 200, 
                'msg' => 'พร้อมสำหรับการพิมพ์',
                'data' => [
                    'print_url' => $label->file_url,
                    'tracking_number' => $label->tracking_number,
                    'format' => $label->label_format
                ]
            ];
        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Regenerate label if needed
     */
    public function regenerate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label_id' => 'required|integer|exists:shipping_labels,id'
        ]);

        if ($validator->fails()) {
            $resp = ['success' => 0, 'code' => 301, 'msg' => 'Invalid label ID'];
            return $this->resp($request, $resp);
        }

        $labelId = $request->get('label_id');
        $label = $this->labelRepository->find($labelId);

        if (!$label) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบป้ายขนส่ง'];
            return $this->resp($request, $resp);
        }

        try {
            // Delete old label file
            if ($label->fileExists()) {
                Storage::disk('public')->delete($label->label_path);
            }

            // Get quote for regeneration
            $quote = $this->quoteRepository->getSelectedQuote($label->shipment_id);
            if (!$quote) {
                $resp = ['success' => 0, 'code' => 400, 'msg' => 'ไม่พบใบเสนอราคาที่เลือก'];
                return $this->resp($request, $resp);
            }

            // Regenerate label
            $labelData = $this->generateCarrierLabel($quote);

            if ($labelData) {
                // Store new label file
                $fileName = 'label_' . $label->shipment_id . '_' . $label->carrier_id . '_' . time() . '.' . $labelData['format'];
                $filePath = 'shipping_labels/' . date('Y/m') . '/' . $fileName;
                
                Storage::disk('public')->put($filePath, $labelData['content']);

                // Update label record
                $this->labelRepository->updateLabel($labelId, [
                    'tracking_number' => $labelData['tracking_number'],
                    'label_format' => strtoupper($labelData['format']),
                    'label_path' => $filePath,
                    'label_data' => $labelData['base64_data'] ?? null,
                    'generated_at' => now()
                ]);

                $resp = [
                    'success' => 1, 
                    'code' => 200, 
                    'msg' => 'สร้างป้ายขนส่งใหม่สำเร็จ',
                    'data' => [
                        'tracking_number' => $labelData['tracking_number'],
                        'download_url' => route('shipper.labels.download', $labelId)
                    ]
                ];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'ไม่สามารถสร้างป้ายขนส่งใหม่ได้'];
            }

        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Show label generation interface
     */
    public function showGenerate($shipmentId)
    {
        $adminInit = $this->adminInit();
        
        // Get selected quote for the shipment
        $selectedQuote = $this->quoteRepository->getSelectedQuote($shipmentId);
        
        if (!$selectedQuote) {
            abort(404, 'No selected quote found for this shipment');
        }

        // Check if label already exists
        $existingLabel = $this->labelRepository->getLabelByShipment($shipmentId, $selectedQuote->carrier_id);

        return view('shipper::labels.generate', [
            'adminInit' => $adminInit,
            'shipmentId' => $shipmentId,
            'selectedQuote' => $selectedQuote,
            'existingLabel' => $existingLabel
        ]);
    }

    /**
     * Generate label from carrier API (placeholder implementation)
     */
    private function generateCarrierLabel($quote)
    {
        // This is a placeholder - actual implementation will be in API service
        // For now, return dummy data for testing
        
        $trackingNumber = strtoupper($quote->carrier->code) . date('Ymd') . rand(1000, 9999);
        
        // Create dummy PDF content
        $pdfContent = base64_decode('JVBERi0xLjQKJeLjz9MKNSAwIG9iago8PA=='); // Minimal PDF header
        
        return [
            'tracking_number' => $trackingNumber,
            'format' => 'pdf',
            'content' => $pdfContent,
            'base64_data' => base64_encode($pdfContent),
            'carrier_response' => [
                'status' => 'success',
                'timestamp' => now()->toISOString()
            ]
        ];
    }
} 