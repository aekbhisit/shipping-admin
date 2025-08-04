<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\Receiver;
use Modules\Customer\Entities\Address;

class ReceiverController extends Controller
{
    /**
     * Search receivers
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $customerId = $request->get('customer_id');
        $minLength = config('customer.search.min_length');

        if (strlen($search) < $minLength) {
            return response()->json([]);
        }

        $query = Receiver::where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $receivers = $query->with(['customer', 'address'])
            ->limit(10)
            ->get()
            ->map(function ($receiver) {
                return [
                    'id' => $receiver->id,
                    'text' => $receiver->name . ' (' . $receiver->phone . ')',
                    'name' => $receiver->name,
                    'phone' => $receiver->phone,
                    'email' => $receiver->email,
                    'customer_name' => $receiver->customer->name,
                    'address' => $receiver->address ? [
                        'id' => $receiver->address->id,
                        'full_address' => $receiver->address->getFullAddress(),
                    ] : null,
                ];
            });

        return response()->json($receivers);
    }

    /**
     * Show the form for creating a new receiver
     */
    public function create(Request $request)
    {
        $customerId = $request->get('customer_id');
        $customer = null;
        
        if ($customerId) {
            $customer = Customer::find($customerId);
        }

        return view('customer::admin.receivers.create', compact('customer'));
    }

    /**
     * Store a newly created receiver
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), array_merge(
            config('customer.validation.receiver'),
            config('customer.validation.address')
        ));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $receiver = Receiver::create([
                'customer_id' => $request->customer_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'created_by_user_id' => Auth::id(),
                'status' => 'active',
            ]);

            // Create address for receiver
            $address = Address::create([
                'receiver_id' => $receiver->id,
                'street' => $request->street,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'is_default' => true,
                'created_by_user_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('admin.customers.receivers.show', $receiver->id)
                ->with('success', 'Receiver created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error creating receiver: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified receiver
     */
    public function show($id)
    {
        $receiver = Receiver::with(['customer', 'address'])
            ->findOrFail($id);

        return view('customer::admin.receivers.show', compact('receiver'));
    }

    /**
     * Show the form for editing the specified receiver
     */
    public function edit($id)
    {
        $receiver = Receiver::with(['customer', 'address'])
            ->findOrFail($id);

        return view('customer::admin.receivers.edit', compact('receiver'));
    }

    /**
     * Update the specified receiver
     */
    public function update(Request $request, $id)
    {
        $receiver = Receiver::findOrFail($id);
        
        $validator = Validator::make($request->all(), array_merge(
            config('customer.validation.receiver'),
            config('customer.validation.address')
        ));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $receiver->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'updated_by_user_id' => Auth::id(),
            ]);

            // Update or create address
            if ($receiver->address) {
                $receiver->address->update([
                    'street' => $request->street,
                    'city' => $request->city,
                    'state' => $request->state,
                    'postal_code' => $request->postal_code,
                    'country' => $request->country,
                ]);
            } else {
                Address::create([
                    'receiver_id' => $receiver->id,
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

            return redirect()->route('admin.customers.receivers.show', $receiver->id)
                ->with('success', 'Receiver updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error updating receiver: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Frequent receivers list
     */
    public function frequent(Request $request)
    {
        $customerId = $request->get('customer_id');
        $limit = config('customer.search.recent_limit');

        $query = Receiver::where('status', 'active')
            ->with(['customer', 'address']);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $frequentReceivers = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($receiver) {
                return [
                    'id' => $receiver->id,
                    'name' => $receiver->name,
                    'phone' => $receiver->phone,
                    'email' => $receiver->email,
                    'customer_name' => $receiver->customer->name,
                    'address' => $receiver->address ? [
                        'id' => $receiver->address->id,
                        'full_address' => $receiver->address->getFullAddress(),
                    ] : null,
                    'created_at' => $receiver->created_at->format('M d, Y'),
                ];
            });

        return response()->json($frequentReceivers);
    }

    /**
     * Get receiver details for shipment form
     */
    public function getDetails($id)
    {
        $receiver = Receiver::with(['customer', 'address'])
            ->where('status', 'active')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'receiver' => [
                'id' => $receiver->id,
                'name' => $receiver->name,
                'phone' => $receiver->phone,
                'email' => $receiver->email,
                'customer_name' => $receiver->customer->name,
                'address' => $receiver->address ? [
                    'id' => $receiver->address->id,
                    'street' => $receiver->address->street,
                    'city' => $receiver->address->city,
                    'state' => $receiver->address->state,
                    'postal_code' => $receiver->address->postal_code,
                    'country' => $receiver->address->country,
                    'full_address' => $receiver->address->getFullAddress(),
                ] : null,
            ],
        ]);
    }

    /**
     * Quick receiver creation for shipment form
     */
    public function quickAdd(Request $request)
    {
        $validator = Validator::make($request->all(), array_merge(
            config('customer.validation.receiver'),
            config('customer.validation.address')
        ));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $receiver = Receiver::create([
                'customer_id' => $request->customer_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'created_by_user_id' => Auth::id(),
                'status' => 'active',
            ]);

            // Create address for receiver
            $address = Address::create([
                'receiver_id' => $receiver->id,
                'street' => $request->street,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'is_default' => true,
                'created_by_user_id' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Receiver created successfully.',
                'receiver' => [
                    'id' => $receiver->id,
                    'name' => $receiver->name,
                    'phone' => $receiver->phone,
                    'email' => $receiver->email,
                    'address' => [
                        'id' => $address->id,
                        'full_address' => $address->getFullAddress(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating receiver: ' . $e->getMessage(),
            ], 500);
        }
    }
} 