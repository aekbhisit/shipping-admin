<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\AddOnService;
use Modules\Product\Entities\BranchAddOn;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * AddOnController
 * Purpose: Additional service management and selection
 * Access Level: Branch Staff and Admin
 */
class AddOnController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:branch_staff|company_admin']);
    }

    /**
     * Display a listing of available add-on services
     * UI Implementation: Grid view with service types and pricing
     */
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        
        $query = AddOnService::with(['branchAddOns' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->active();

        // Service type filter
        if ($request->has('service_type') && $request->service_type != '') {
            $query->byServiceType($request->service_type);
        }

        // Pricing type filter
        if ($request->has('pricing_type') && $request->pricing_type != '') {
            $query->byPricingType($request->pricing_type);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        $addOns = $query->ordered()->paginate(20);
        
        // Get service type options
        $serviceTypes = [
            'insurance' => 'Insurance Services',
            'handling' => 'Special Handling',
            'delivery' => 'Delivery Options',
            'cod' => 'COD Services'
        ];

        return view('product::admin.addons.index', compact('addOns', 'serviceTypes'));
    }

    /**
     * Show the form for creating a new add-on service
     * UI Implementation: Add-on service form with pricing options
     */
    public function create()
    {
        $serviceTypes = [
            'insurance' => 'Insurance Services',
            'handling' => 'Special Handling',
            'delivery' => 'Delivery Options',
            'cod' => 'COD Services'
        ];

        $pricingTypes = [
            'fixed' => 'Fixed Price',
            'percentage' => 'Percentage of Base Amount',
            'tiered' => 'Tiered Pricing'
        ];

        return view('product::admin.addons.create', compact('serviceTypes', 'pricingTypes'));
    }

    /**
     * Store a newly created add-on service
     * Business Logic: Validate pricing rules, handle restrictions
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|in:insurance,handling,delivery,cod',
            'pricing_type' => 'required|in:fixed,percentage,tiered',
            'base_price' => 'required_if:pricing_type,fixed|nullable|numeric|min:0',
            'percentage_rate' => 'required_if:pricing_type,percentage|nullable|numeric|min:0|max:100',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'requirements' => 'nullable|array',
            'restrictions' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'name', 'description', 'service_type', 'pricing_type',
            'base_price', 'percentage_rate', 'min_amount', 'max_amount',
            'sort_order', 'requirements', 'restrictions'
        ]);
        
        $data['is_active'] = $request->has('is_active');

        AddOnService::create($data);

        return redirect()->route('admin.addons.index')
            ->with('success', 'Add-on service created successfully.');
    }

    /**
     * Display the specified add-on service
     * Display: Service details, pricing, branch availability
     */
    public function show(AddOnService $addOn)
    {
        $branchId = Auth::user()->branch_id;
        
        $addOn->load(['branchAddOns' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }]);

        $branchAddOn = $addOn->branchAddOns->first();

        return view('product::admin.addons.show', compact('addOn', 'branchAddOn'));
    }

    /**
     * Show the form for editing the specified add-on service
     * UI Implementation: Add-on service form with current values
     */
    public function edit(AddOnService $addOn)
    {
        $serviceTypes = [
            'insurance' => 'Insurance Services',
            'handling' => 'Special Handling',
            'delivery' => 'Delivery Options',
            'cod' => 'COD Services'
        ];

        $pricingTypes = [
            'fixed' => 'Fixed Price',
            'percentage' => 'Percentage of Base Amount',
            'tiered' => 'Tiered Pricing'
        ];

        return view('product::admin.addons.edit', compact('addOn', 'serviceTypes', 'pricingTypes'));
    }

    /**
     * Update the specified add-on service
     * Business Logic: Validate pricing changes, update restrictions
     */
    public function update(Request $request, AddOnService $addOn)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|in:insurance,handling,delivery,cod',
            'pricing_type' => 'required|in:fixed,percentage,tiered',
            'base_price' => 'required_if:pricing_type,fixed|nullable|numeric|min:0',
            'percentage_rate' => 'required_if:pricing_type,percentage|nullable|numeric|min:0|max:100',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'requirements' => 'nullable|array',
            'restrictions' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'name', 'description', 'service_type', 'pricing_type',
            'base_price', 'percentage_rate', 'min_amount', 'max_amount',
            'sort_order', 'requirements', 'restrictions'
        ]);
        
        $data['is_active'] = $request->has('is_active');

        $addOn->update($data);

        return redirect()->route('admin.addons.index')
            ->with('success', 'Add-on service updated successfully.');
    }

    /**
     * Calculate add-on pricing
     * Business Logic: Calculate price based on service type and parameters
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'add_on_id' => 'required|exists:add_on_services,id',
            'base_amount' => 'required|numeric|min:0',
            'shipment_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $addOn = AddOnService::find($request->add_on_id);
        $branchId = Auth::user()->branch_id;
        $baseAmount = $request->base_amount;
        $shipmentData = $request->shipment_data ?? [];

        // Check if add-on is available
        if (!$addOn->isAvailable($shipmentData)) {
            return response()->json([
                'success' => false,
                'message' => 'Add-on service is not available for this shipment.'
            ], 400);
        }

        // Calculate price
        $price = $addOn->calculatePrice($baseAmount, $branchId);

        return response()->json([
            'success' => true,
            'add_on' => [
                'id' => $addOn->id,
                'name' => $addOn->name,
                'service_type' => $addOn->service_type,
                'pricing_type' => $addOn->pricing_type,
                'price' => $price,
                'formatted_price' => '฿' . number_format($price, 2)
            ]
        ]);
    }

    /**
     * Apply add-on to shipment
     * Business Logic: Add add-on to shipment with calculated pricing
     */
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'add_on_id' => 'required|exists:add_on_services,id',
            'shipment_id' => 'required|exists:shipments,id',
            'base_amount' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $addOn = AddOnService::find($request->add_on_id);
        $branchId = Auth::user()->branch_id;
        $baseAmount = $request->base_amount;
        $quantity = $request->quantity ?? 1;

        // Calculate total price
        $unitPrice = $addOn->calculatePrice($baseAmount, $branchId);
        $totalPrice = $unitPrice * $quantity;

        // Create shipment add-on record
        $shipmentAddOn = \Modules\Shipment\Entities\ShipmentAddOn::create([
            'shipment_id' => $request->shipment_id,
            'add_on_service_id' => $addOn->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Add-on service applied successfully.',
            'shipment_add_on' => $shipmentAddOn
        ]);
    }

    /**
     * Toggle add-on active status
     */
    public function toggleStatus(Request $request, AddOnService $addOn)
    {
        $addOn->update([
            'is_active' => !$addOn->is_active
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $addOn->is_active ? 'activated' : 'deactivated'
            ]);
        }

        $statusText = $addOn->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Add-on service {$statusText} successfully.");
    }

    /**
     * Remove the specified add-on service
     * Business Logic: Check for usage in shipments
     */
    public function destroy(AddOnService $addOn)
    {
        // Check if add-on is being used in any shipments
        if ($addOn->shipmentAddOns()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete add-on service that is being used in shipments.');
        }

        $addOn->delete();

        return redirect()->route('admin.addons.index')
            ->with('success', 'Add-on service deleted successfully.');
    }

    /**
     * Get available add-ons for shipment
     */
    public function getAvailableAddOns(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $shipmentData = $request->all();

        $addOns = AddOnService::with(['branchAddOns' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->active()->get();

        $availableAddOns = $addOns->filter(function($addOn) use ($shipmentData) {
            return $addOn->isAvailable($shipmentData);
        });

        return response()->json([
            'success' => true,
            'add_ons' => $availableAddOns->values()
        ]);
    }
} 