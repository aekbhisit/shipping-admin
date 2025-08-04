<?php

namespace Modules\Shipper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Shipper\Entities\Carrier;
use Modules\Shipper\Entities\CarrierCredential;
use Modules\Shipper\Services\CarrierApiService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * CarrierCredentialController
 * Purpose: Branch admin manages carrier credentials for their branch
 * Access Level: Branch Admin
 */
class CarrierCredentialController extends Controller
{
    protected $carrierApiService;

    public function __construct(CarrierApiService $carrierApiService)
    {
        $this->carrierApiService = $carrierApiService;
        $this->middleware(['auth', 'role:branch_admin', 'branch.isolation']);
    }

    /**
     * Display branch carrier credentials
     * UI Implementation: Simple list with enable/disable toggle
     * Scope: Current branch credentials only
     */
    public function index()
    {
        $branchId = Auth::user()->branch_id;
        
        // Get all active carriers
        $carriers = Carrier::active()->orderBy('name')->get();
        
        // Get existing credentials for this branch
        $credentials = CarrierCredential::byBranch($branchId)
            ->with('carrier')
            ->orderBy('carrier_id')
            ->get()
            ->keyBy('carrier_id');

        return view('shipper::branch.credentials.index', compact('carriers', 'credentials', 'branchId'));
    }

    /**
     * Show the form for creating carrier credentials
     * UI Implementation: Basic forms with essential fields only
     * Credential Format: JSON input for API credentials
     */
    public function create(Request $request)
    {
        $carrierId = $request->get('carrier_id');
        $carrier = Carrier::findOrFail($carrierId);
        
        return view('shipper::branch.credentials.create', compact('carrier'));
    }

    /**
     * Store carrier credentials
     * Validation: Basic format validation only
     * Security: Encrypt sensitive credential data
     */
    public function store(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        
        $validator = Validator::make($request->all(), [
            'carrier_id' => 'required|exists:carriers,id',
            'credentials' => 'required|array',
            'credentials.*' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if credentials already exist for this branch/carrier
        $existing = CarrierCredential::where('branch_id', $branchId)
            ->where('carrier_id', $request->carrier_id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withErrors(['carrier_id' => 'Credentials for this carrier already exist.'])
                ->withInput();
        }

        CarrierCredential::create([
            'branch_id' => $branchId,
            'carrier_id' => $request->carrier_id,
            'credentials' => $request->credentials,
            'is_active' => true,
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('admin.branch.credentials.index')
            ->with('success', 'Carrier credentials created successfully.');
    }

    /**
     * Show the form for editing carrier credentials
     * UI Implementation: Basic forms with masked credentials
     */
    public function edit(CarrierCredential $credential)
    {
        // Ensure credential belongs to current branch
        if ($credential->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized access to credential.');
        }

        $carrier = $credential->carrier;
        
        return view('shipper::branch.credentials.edit', compact('credential', 'carrier'));
    }

    /**
     * Update carrier credentials
     * Audit Trail: Log credential changes
     */
    public function update(Request $request, CarrierCredential $credential)
    {
        // Ensure credential belongs to current branch
        if ($credential->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized access to credential.');
        }

        $validator = Validator::make($request->all(), [
            'credentials' => 'required|array',
            'credentials.*' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credential->update([
            'credentials' => $request->credentials,
            'updated_by' => Auth::id(),
            // Reset test results when credentials are updated
            'last_tested_at' => null,
            'test_result' => null,
            'test_error_message' => null
        ]);

        return redirect()->route('admin.branch.credentials.index')
            ->with('success', 'Carrier credentials updated successfully.');
    }

    /**
     * Toggle credential active status
     */
    public function toggleStatus(CarrierCredential $credential)
    {
        // Ensure credential belongs to current branch
        if ($credential->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized access to credential.');
        }

        $credential->update([
            'is_active' => !$credential->is_active,
            'updated_by' => Auth::id()
        ]);

        $statusText = $credential->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Carrier credentials {$statusText} successfully.");
    }

    /**
     * Test API connection
     * UI Implementation: Basic test quote with pass/fail result
     * Implementation: Basic availability check on API call
     */
    public function testConnection(CarrierCredential $credential)
    {
        // Ensure credential belongs to current branch
        if ($credential->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized access to credential.');
        }

        try {
            // Test the connection with sample package data
            $testPackageData = [
                'weight' => 1.0,
                'length' => 10,
                'width' => 10,
                'height' => 10,
                'pickup_postcode' => '10110',
                'delivery_postcode' => '10120'
            ];

            $result = $this->carrierApiService->testCarrierConnection(
                $credential->carrier_id, 
                $credential->branch_id
            );

            if ($result) {
                $credential->updateTestResult(true);
                $message = 'Connection test successful!';
                $type = 'success';
            } else {
                $credential->updateTestResult(false, 'Connection failed');
                $message = 'Connection test failed. Please check your credentials.';
                $type = 'error';
            }

        } catch (\Exception $e) {
            $credential->updateTestResult(false, $e->getMessage());
            $message = 'Connection test failed: ' . $e->getMessage();
            $type = 'error';
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => $result ?? false,
                'message' => $message,
                'test_result' => $credential->fresh()->test_result,
                'test_error' => $credential->fresh()->test_error_message
            ]);
        }

        return redirect()->back()->with($type, $message);
    }

    /**
     * Show test results page
     * UI Implementation: Basic test quote with pass/fail result
     * Features: test_connection, view_results
     */
    public function showTestResults(CarrierCredential $credential)
    {
        // Ensure credential belongs to current branch
        if ($credential->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized access to credential.');
        }

        return view('shipper::branch.credentials.test', compact('credential'));
    }

    /**
     * Delete carrier credentials
     */
    public function destroy(CarrierCredential $credential)
    {
        // Ensure credential belongs to current branch
        if ($credential->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized access to credential.');
        }

        $credential->delete();

        return redirect()->route('admin.branch.credentials.index')
            ->with('success', 'Carrier credentials deleted successfully.');
    }
} 