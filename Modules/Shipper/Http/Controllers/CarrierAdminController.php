<?php

namespace Modules\Shipper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Shipper\Entities\Carrier;
use Illuminate\Support\Facades\Validator;

/**
 * CarrierAdminController
 * Purpose: Company admin manages global carrier configuration
 * Access Level: Company Admin
 */
class CarrierAdminController extends Controller
{
    /**
     * Display a listing of carriers with DataTable interface
     * UI Implementation: DataTable interface for admin management
     * Features: enable_disable_toggle, status_filter
     */
    public function index()
    {
        $carriers = Carrier::orderBy('name')->get();
        
        return view('shipper::admin.carriers.index', compact('carriers'));
    }

    /**
     * Show the form for creating a new carrier
     * UI Implementation: Basic forms with essential fields only
     * Validation: Basic format validation only
     */
    public function create()
    {
        return view('shipper::admin.carriers.create');
    }

    /**
     * Store a newly created carrier in storage
     * Validation: Basic required field validation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:carriers,code',
            'api_base_url' => 'required|url|max:500',
            'api_version' => 'nullable|string|max:20',
            'supported_services' => 'nullable|array',
            'api_documentation_url' => 'nullable|url|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'name', 'code', 'api_base_url', 'api_version', 
            'supported_services', 'api_documentation_url'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('carriers/logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        // Default to active
        $data['is_active'] = true;

        Carrier::create($data);

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Carrier created successfully.');
    }

    /**
     * Display the specified carrier
     * Display: Carrier info, supported services, API documentation
     */
    public function show(Carrier $carrier)
    {
        $carrier->load(['carrierCredentials', 'quoteRequests' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('shipper::admin.carriers.show', compact('carrier'));
    }

    /**
     * Show the form for editing the specified carrier
     * UI Implementation: Basic forms with essential fields only
     */
    public function edit(Carrier $carrier)
    {
        return view('shipper::admin.carriers.edit', compact('carrier'));
    }

    /**
     * Update the specified carrier in storage
     * Audit Trail: Log changes
     */
    public function update(Request $request, Carrier $carrier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:carriers,code,' . $carrier->id,
            'api_base_url' => 'required|url|max:500',
            'api_version' => 'nullable|string|max:20',
            'supported_services' => 'nullable|array',
            'api_documentation_url' => 'nullable|url|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'name', 'code', 'api_base_url', 'api_version', 
            'supported_services', 'api_documentation_url'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('carriers/logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        $carrier->update($data);

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Carrier updated successfully.');
    }

    /**
     * Remove the specified carrier from storage
     * Implementation: Soft delete - mark as inactive
     */
    public function destroy(Carrier $carrier)
    {
        $carrier->update(['is_active' => false]);

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Carrier deactivated successfully.');
    }

    /**
     * Toggle carrier active status
     * AJAX endpoint for DataTable enable/disable toggle
     */
    public function toggleStatus(Request $request, Carrier $carrier)
    {
        $carrier->update([
            'is_active' => !$carrier->is_active
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $carrier->is_active ? 'activated' : 'deactivated'
            ]);
        }

        $statusText = $carrier->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Carrier {$statusText} successfully.");
    }

    /**
     * Get carriers data for DataTable AJAX
     */
    public function getCarriersData(Request $request)
    {
        $query = Carrier::query();

        // Apply status filter if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status == 'active');
        }

        // Apply search filter
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('code', 'like', "%{$searchValue}%");
            });
        }

        $carriers = $query->orderBy('name')->get();

        return response()->json([
            'data' => $carriers->map(function($carrier) {
                return [
                    'id' => $carrier->id,
                    'name' => $carrier->name,
                    'code' => $carrier->code,
                    'api_base_url' => $carrier->api_base_url,
                    'is_active' => $carrier->is_active,
                    'status_badge' => $carrier->is_active ? 
                        '<span class="badge badge-success">Active</span>' : 
                        '<span class="badge badge-secondary">Inactive</span>',
                    'actions' => view('shipper::admin.carriers.partials.actions', compact('carrier'))->render()
                ];
            })
        ]);
    }
} 