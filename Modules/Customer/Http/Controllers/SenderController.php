<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\Sender;
use Modules\Customer\Entities\Address;

class SenderController extends Controller
{
    /**
     * List senders for customer
     */
    public function index(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $senders = $customer->senders()
            ->with('addresses')
            ->orderBy('created_at', 'desc')
            ->paginate(config('customer.pagination.senders_per_page'));

        return view('customer::admin.senders.index', compact('customer', 'senders'));
    }

    /**
     * Show the form for creating a new sender
     */
    public function create(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);
        
        return view('customer::admin.senders.create', compact('customer'));
    }

    /**
     * Store a newly created sender
     */
    public function store(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);
        
        $validator = Validator::make($request->all(), config('customer.validation.sender'));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $sender = Sender::create([
                'customer_id' => $customerId,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'created_by_user_id' => Auth::id(),
                'status' => 'active',
            ]);

            // Create default address if provided
            if ($request->filled('street')) {
                $address = Address::create([
                    'sender_id' => $sender->id,
                    'street' => $request->street,
                    'city' => $request->city,
                    'state' => $request->state,
                    'postal_code' => $request->postal_code,
                    'country' => $request->country,
                    'is_default' => true,
                    'created_by_user_id' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.customers.senders.show', [$customerId, $sender->id])
                ->with('success', 'Sender created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error creating sender: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified sender
     */
    public function show($customerId, $senderId)
    {
        $customer = Customer::findOrFail($customerId);
        $sender = $customer->senders()
            ->with('addresses')
            ->findOrFail($senderId);

        return view('customer::admin.senders.show', compact('customer', 'sender'));
    }

    /**
     * Show the form for editing the specified sender
     */
    public function edit($customerId, $senderId)
    {
        $customer = Customer::findOrFail($customerId);
        $sender = $customer->senders()->findOrFail($senderId);

        return view('customer::admin.senders.edit', compact('customer', 'sender'));
    }

    /**
     * Update the specified sender
     */
    public function update(Request $request, $customerId, $senderId)
    {
        $customer = Customer::findOrFail($customerId);
        $sender = $customer->senders()->findOrFail($senderId);
        
        $validator = Validator::make($request->all(), config('customer.validation.sender'));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $sender->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'updated_by_user_id' => Auth::id(),
            ]);

            return redirect()->route('admin.customers.senders.show', [$customerId, $sender->id])
                ->with('success', 'Sender updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating sender: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Add new address to sender
     */
    public function addAddress(Request $request, $customerId, $senderId)
    {
        $customer = Customer::findOrFail($customerId);
        $sender = $customer->senders()->findOrFail($senderId);
        
        $validator = Validator::make($request->all(), config('customer.validation.address'));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // If this is the first address, make it default
            $isDefault = $sender->addresses()->count() === 0;

            $address = Address::create([
                'sender_id' => $senderId,
                'street' => $request->street,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'is_default' => $isDefault,
                'created_by_user_id' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Address added successfully.',
                'address' => [
                    'id' => $address->id,
                    'street' => $address->street,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'full_address' => $address->getFullAddress(),
                    'is_default' => $address->is_default,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error adding address: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Select default address
     */
    public function selectAddress(Request $request, $customerId, $senderId, $addressId)
    {
        $customer = Customer::findOrFail($customerId);
        $sender = $customer->senders()->findOrFail($senderId);
        $address = $sender->addresses()->findOrFail($addressId);

        try {
            DB::beginTransaction();

            // Remove default from all addresses
            $sender->addresses()->update(['is_default' => false]);

            // Set new default
            $address->update(['is_default' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Default address updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating default address: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sender addresses for AJAX
     */
    public function getAddresses($customerId, $senderId)
    {
        $customer = Customer::findOrFail($customerId);
        $sender = $customer->senders()->findOrFail($senderId);
        
        $addresses = $sender->addresses()->get()->map(function ($address) {
            return [
                'id' => $address->id,
                'street' => $address->street,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
                'full_address' => $address->getFullAddress(),
                'is_default' => $address->is_default,
            ];
        });

        return response()->json($addresses);
    }

    /**
     * Search senders by name or phone
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $customerId = $request->get('customer_id');
        $minLength = config('customer.search.min_length');

        if (strlen($search) < $minLength) {
            return response()->json([]);
        }

        $query = Sender::where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $senders = $query->with(['customer', 'addresses'])
            ->limit(10)
            ->get()
            ->map(function ($sender) {
                return [
                    'id' => $sender->id,
                    'text' => $sender->name . ' (' . $sender->phone . ')',
                    'name' => $sender->name,
                    'phone' => $sender->phone,
                    'email' => $sender->email,
                    'customer_name' => $sender->customer->name,
                    'addresses' => $sender->addresses->map(function ($address) {
                        return [
                            'id' => $address->id,
                            'full_address' => $address->getFullAddress(),
                            'is_default' => $address->is_default,
                        ];
                    }),
                ];
            });

        return response()->json($senders);
    }
} 