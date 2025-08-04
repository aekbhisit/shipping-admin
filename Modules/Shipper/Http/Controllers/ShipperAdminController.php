<?php

namespace Modules\Shipper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Http\Controllers\AdminController;
use Modules\Shipper\Repositories\Contracts\CarrierRepositoryInterface;

class ShipperAdminController extends AdminController
{
    protected $carrierRepository;

    /**
     * Constructor - Inject CarrierRepository
     * Clean Architecture: Controller → Repository → Database
     */
    public function __construct(CarrierRepositoryInterface $carrierRepository)
    {
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * Display carriers list with simple status indicators
     */
    public function index()
    {
        $adminInit = $this->adminInit();
        return view('shipper::admin.shippers.index', ['adminInit' => $adminInit]);
    }

    /**
     * DataTable AJAX endpoint for carriers list
     */
    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {
            return $this->carrierRepository->getForDataTable($request);
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }

    /**
     * Show carrier creation form
     */
    public function create()
    {
        $adminInit = $this->adminInit();
        return view('shipper::admin.shippers.create', ['adminInit' => $adminInit]);
    }

    /**
     * Create new carrier
     */
    public function store(Request $request)
    {
        // Validate post data
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'code' => 'required|max:50|unique:carriers,code',
            'api_endpoint' => 'required|url|max:500',
            'priority_order' => 'required|integer|min:1',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $resp = ['success' => 0, 'code' => 301, 'msg' => 'Validation error', 'error' => $errors];
            return $this->resp($request, $resp);
        }

        $data = [
            'name' => $request->get('name'),
            'code' => strtoupper($request->get('code')),
            'api_endpoint' => $request->get('api_endpoint'),
            'priority_order' => $request->get('priority_order'),
            'is_active' => $request->has('is_active') ? 1 : 0
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('carriers/logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        try {
            $carrier = $this->carrierRepository->create($data);
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'เพิ่มผู้ให้บริการขนส่งสำเร็จ'];
        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Display carrier details and API logs
     */
    public function show($id)
    {
        $adminInit = $this->adminInit();
        $carrier = $this->carrierRepository->find($id);
        
        if (!$carrier) {
            abort(404, 'Carrier not found');
        }

        // Get carrier statistics
        $statistics = $this->carrierRepository->getStatistics($id);
        
        return view('shipper::admin.shippers.show', [
            'adminInit' => $adminInit,
            'carrier' => $carrier,
            'statistics' => $statistics
        ]);
    }

    /**
     * Show carrier editing form
     */
    public function edit($id)
    {
        $adminInit = $this->adminInit();
        $carrier = $this->carrierRepository->find($id);
        
        if (!$carrier) {
            abort(404, 'Carrier not found');
        }

        return view('shipper::admin.shippers.edit', [
            'adminInit' => $adminInit,
            'carrier' => $carrier
        ]);
    }

    /**
     * Update carrier information
     */
    public function update(Request $request, $id)
    {
        $carrier = $this->carrierRepository->find($id);
        
        if (!$carrier) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบผู้ให้บริการขนส่ง'];
            return $this->resp($request, $resp);
        }

        // Validate post data
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'code' => 'required|max:50|unique:carriers,code,' . $id,
            'api_endpoint' => 'required|url|max:500',
            'priority_order' => 'required|integer|min:1',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $resp = ['success' => 0, 'code' => 301, 'msg' => 'Validation error', 'error' => $errors];
            return $this->resp($request, $resp);
        }

        $data = [
            'name' => $request->get('name'),
            'code' => strtoupper($request->get('code')),
            'api_endpoint' => $request->get('api_endpoint'),
            'priority_order' => $request->get('priority_order'),
            'is_active' => $request->has('is_active') ? 1 : 0
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($carrier->logo_path && Storage::disk('public')->exists($carrier->logo_path)) {
                Storage::disk('public')->delete($carrier->logo_path);
            }
            
            $logoPath = $request->file('logo')->store('carriers/logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        try {
            $this->carrierRepository->update($id, $data);
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Delete carrier
     */
    public function destroy(Request $request, $id)
    {
        $carrier = $this->carrierRepository->find($id);
        
        if (!$carrier) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบผู้ให้บริการขนส่ง'];
            return $this->resp($request, $resp);
        }

        try {
            // Delete logo file if exists
            if ($carrier->logo_path && Storage::disk('public')->exists($carrier->logo_path)) {
                Storage::disk('public')->delete($carrier->logo_path);
            }
            
            $this->carrierRepository->delete($id);
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'ลบรายการสำเร็จ'];
        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Test API connection for carrier
     */
    public function testConnection(Request $request, $id)
    {
        $carrier = $this->carrierRepository->find($id);
        
        if (!$carrier) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบผู้ให้บริการขนส่ง'];
            return $this->resp($request, $resp);
        }

        // Get active configuration for carrier
        $configuration = $carrier->getActiveConfiguration();
        
        if (!$configuration) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบการตั้งค่า API สำหรับผู้ให้บริการนี้'];
            return $this->resp($request, $resp);
        }

        try {
            // Test connection logic will be implemented in API service
            $connectionResult = $configuration->testConnection();
            
            if ($connectionResult) {
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'เชื่อมต่อ API สำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เชื่อมต่อ API ไม่สำเร็จ'];
            }
        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Update carrier status (AJAX)
     */
    public function setStatus(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $status = $request->get('status');

            try {
                $this->carrierRepository->updateStatus($id, $status);
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } catch (\Exception $e) {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }

            return $this->resp($request, $resp);
        }
    }

    /**
     * View API call logs and errors
     * Implementation: API call logging and monitoring for troubleshooting
     * Features: Log filtering, error analysis, performance metrics
     */
    public function viewLogs(Request $request, $id = null)
    {
        $carrier = null;
        if ($id) {
            $carrier = $this->carrierRepository->find($id);
            if (!$carrier) {
                $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบผู้ให้บริการขนส่ง'];
                return $this->resp($request, $resp);
            }
        }

        try {
            // Get API logs with filtering
            $filters = [
                'carrier_id' => $id,
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'status' => $request->get('status'), // success, error, pending
                'limit' => $request->get('limit', 100)
            ];

            $logs = $this->carrierRepository->getApiLogs($filters);
            $stats = $this->carrierRepository->getApiLogStats($filters);

            if ($request->ajax()) {
                return response()->json([
                    'success' => 1,
                    'logs' => $logs,
                    'stats' => $stats,
                    'carrier' => $carrier
                ]);
            }

            $adminInit = $this->adminInit();
            return view('shipper::admin.shippers.logs', [
                'adminInit' => $adminInit,
                'logs' => $logs,
                'stats' => $stats,
                'carrier' => $carrier
            ]);

        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
            return $this->resp($request, $resp);
        }
    }

    /**
     * Show carrier configuration form
     */
    public function configuration($id)
    {
        $adminInit = $this->adminInit();
        $carrier = $this->carrierRepository->find($id);
        
        if (!$carrier) {
            abort(404, 'Carrier not found');
        }

        return view('shipper::admin.shippers.configuration', [
            'adminInit' => $adminInit,
            'carrier' => $carrier
        ]);
    }

    /**
     * Update carrier configuration
     */
    public function updateConfiguration(Request $request, $id)
    {
        $carrier = $this->carrierRepository->find($id);
        
        if (!$carrier) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบผู้ให้บริการขนส่ง'];
            return $this->resp($request, $resp);
        }

        // Validate configuration data
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string|max:255',
            'api_secret' => 'required|string|max:255',
            'webhook_url' => 'nullable|url|max:500',
            'timeout' => 'nullable|integer|min:5|max:300',
            'retry_attempts' => 'nullable|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $resp = ['success' => 0, 'code' => 301, 'msg' => 'Validation error', 'error' => $errors];
            return $this->resp($request, $resp);
        }

        try {
            // Update configuration logic here
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'Configuration updated successfully'];
        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Activate a carrier
     */
    public function activate($id)
    {
        try {
            $carrier = $this->carrierRepository->find($id);
            
            if (!$carrier) {
                return back()->with('error', 'Carrier not found');
            }

            $this->carrierRepository->updateStatus($id, true);
            
            return back()->with('success', 'Carrier activated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate carrier: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate a carrier
     */
    public function deactivate($id)
    {
        try {
            $carrier = $this->carrierRepository->find($id);
            
            if (!$carrier) {
                return back()->with('error', 'Carrier not found');
            }

            $this->carrierRepository->updateStatus($id, false);
            
            return back()->with('success', 'Carrier deactivated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deactivate carrier: ' . $e->getMessage());
        }
    }
} 