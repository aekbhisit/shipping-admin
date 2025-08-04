<?php

namespace Modules\Shipper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Http\Controllers\AdminController;
use Modules\Shipper\Repositories\Contracts\CarrierConfigurationRepositoryInterface;
use Modules\Shipper\Repositories\Contracts\CarrierRepositoryInterface;

class CarrierConfigurationAdminController extends AdminController
{
    protected $configurationRepository;
    protected $carrierRepository;

    /**
     * Constructor - Inject Repositories
     */
    public function __construct(
        CarrierConfigurationRepositoryInterface $configurationRepository,
        CarrierRepositoryInterface $carrierRepository
    ) {
        $this->configurationRepository = $configurationRepository;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * Display API configurations per carrier/branch
     */
    public function index()
    {
        $adminInit = $this->adminInit();
        $carriers = $this->carrierRepository->all();
        
        return view('shipper::admin.configurations.index', [
            'adminInit' => $adminInit,
            'carriers' => $carriers
        ]);
    }

    /**
     * DataTable AJAX endpoint for configurations list
     */
    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {
            return $this->configurationRepository->getForDataTable($request->all());
        }
    }

    /**
     * Show simple credential form
     */
    public function create()
    {
        $adminInit = $this->adminInit();
        $carriers = $this->carrierRepository->all();
        
        // Get available branches (assuming branches exist)
        $branches = collect(); // Will be populated from Branch module when available
        
        return view('shipper::admin.configurations.create', [
            'adminInit' => $adminInit,
            'carriers' => $carriers,
            'branches' => $branches
        ]);
    }

    /**
     * Create API configuration
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'carrier_id' => 'required|integer|exists:carriers,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'api_username' => 'nullable|string|max:255',
            'api_password' => 'nullable|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255'
        ]);

        // Custom validation: at least one credential must be provided
        $validator->after(function ($validator) use ($request) {
            if (!$request->api_username && !$request->api_password && 
                !$request->api_key && !$request->api_secret) {
                $validator->errors()->add('credentials', 'กรุณาระบุข้อมูลการเชื่อมต่อ API อย่างน้อย 1 รายการ');
            }
        });

        // Check for existing configuration
        $validator->after(function ($validator) use ($request) {
            $existing = $this->configurationRepository->getConfiguration(
                $request->carrier_id, 
                $request->branch_id
            );
            if ($existing) {
                $validator->errors()->add('duplicate', 'มีการตั้งค่าสำหรับผู้ให้บริการและสาขานี้แล้ว');
            }
        });

        if ($validator->fails()) {
            $resp = ['success' => 0, 'code' => 301, 'msg' => 'Validation error', 'error' => $validator->errors()];
            return $this->resp($request, $resp);
        }

        try {
            $data = [
                'carrier_id' => $request->get('carrier_id'),
                'branch_id' => $request->get('branch_id'),
                'api_username' => $request->get('api_username'),
                'api_password' => $request->get('api_password'),
                'api_key' => $request->get('api_key'),
                'api_secret' => $request->get('api_secret'),
                'is_active' => $request->has('is_active') ? 1 : 0,
                'created_by' => Auth::guard('admin')->id()
            ];

            $configuration = $this->configurationRepository->createConfiguration($data);
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'เพิ่มการตั้งค่า API สำเร็จ'];

        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Show credential editing form
     */
    public function edit($id)
    {
        $adminInit = $this->adminInit();
        $configuration = $this->configurationRepository->find($id);
        
        if (!$configuration) {
            abort(404, 'Configuration not found');
        }

        $carriers = $this->carrierRepository->all();
        $branches = collect(); // Will be populated from Branch module when available

        return view('shipper::admin.configurations.edit', [
            'adminInit' => $adminInit,
            'configuration' => $configuration,
            'carriers' => $carriers,
            'branches' => $branches
        ]);
    }

    /**
     * Update API credentials
     */
    public function update(Request $request, $id)
    {
        $configuration = $this->configurationRepository->find($id);
        
        if (!$configuration) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบการตั้งค่า'];
            return $this->resp($request, $resp);
        }

        $validator = Validator::make($request->all(), [
            'carrier_id' => 'required|integer|exists:carriers,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'api_username' => 'nullable|string|max:255',
            'api_password' => 'nullable|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255'
        ]);

        // Custom validation: at least one credential must be provided
        $validator->after(function ($validator) use ($request) {
            if (!$request->api_username && !$request->api_password && 
                !$request->api_key && !$request->api_secret) {
                $validator->errors()->add('credentials', 'กรุณาระบุข้อมูลการเชื่อมต่อ API อย่างน้อย 1 รายการ');
            }
        });

        // Check for duplicate configuration (excluding current)
        $validator->after(function ($validator) use ($request, $id) {
            $existing = $this->configurationRepository->getConfiguration(
                $request->carrier_id, 
                $request->branch_id
            );
            if ($existing && $existing->id != $id) {
                $validator->errors()->add('duplicate', 'มีการตั้งค่าสำหรับผู้ให้บริการและสาขานี้แล้ว');
            }
        });

        if ($validator->fails()) {
            $resp = ['success' => 0, 'code' => 301, 'msg' => 'Validation error', 'error' => $validator->errors()];
            return $this->resp($request, $resp);
        }

        try {
            $data = [
                'carrier_id' => $request->get('carrier_id'),
                'branch_id' => $request->get('branch_id'),
                'api_username' => $request->get('api_username'),
                'api_password' => $request->get('api_password'),
                'api_key' => $request->get('api_key'),
                'api_secret' => $request->get('api_secret'),
                'is_active' => $request->has('is_active') ? 1 : 0
            ];

            $this->configurationRepository->updateConfiguration($id, $data);
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];

        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Delete configuration
     */
    public function destroy(Request $request, $id)
    {
        $configuration = $this->configurationRepository->find($id);
        
        if (!$configuration) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบการตั้งค่า'];
            return $this->resp($request, $resp);
        }

        try {
            $this->configurationRepository->deleteConfiguration($id);
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'ลบการตั้งค่าสำเร็จ'];

        } catch (\Exception $e) {
            $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }

        return $this->resp($request, $resp);
    }

    /**
     * Test configuration connection
     */
    public function testConnection(Request $request, $id)
    {
        $configuration = $this->configurationRepository->find($id);
        
        if (!$configuration) {
            $resp = ['success' => 0, 'code' => 404, 'msg' => 'ไม่พบการตั้งค่า'];
            return $this->resp($request, $resp);
        }

        try {
            // Test connection using the configuration
            $result = $this->configurationRepository->testConfiguration($id);
            
            if ($result) {
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
     * Update configuration status (AJAX)
     */
    public function setStatus(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $status = $request->get('status');

            try {
                $this->configurationRepository->updateConfiguration($id, ['is_active' => $status]);
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } catch (\Exception $e) {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }

            return $this->resp($request, $resp);
        }
    }
} 