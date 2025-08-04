<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\Sender;
use Modules\Customer\Entities\Receiver;

class CustomerController extends Controller
{
    /**
     * Quick customer search for shipments
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $minLength = config('customer.search.min_length');
        $maxResults = config('customer.search.max_results');

        if (strlen($search) < $minLength) {
            return response()->json([]);
        }

        $customers = Customer::where('status', 'active')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('tax_id', 'like', "%{$search}%");
            })
            ->with(['senders.addresses', 'branch'])
            ->limit($maxResults)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'text' => $customer->name . ' (' . $customer->customer_type . ')',
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'customer_type' => $customer->customer_type,
                    'senders' => $customer->senders->map(function ($sender) {
                        return [
                            'id' => $sender->id,
                            'name' => $sender->name,
                            'phone' => $sender->phone,
                            'addresses' => $sender->addresses->map(function ($address) {
                                return [
                                    'id' => $address->id,
                                    'full_address' => $address->getFullAddress(),
                                    'is_default' => $address->is_default,
                                ];
                            }),
                        ];
                    }),
                    'branch_name' => $customer->branch->name ?? 'N/A',
                ];
            });

        return response()->json($customers);
    }

    /**
     * AJAX autocomplete for forms
     */
    public function autocomplete(Request $request)
    {
        $search = $request->get('term');
        $minLength = config('customer.search.min_length');
        $maxResults = config('customer.search.max_results');

        if (strlen($search) < $minLength) {
            return response()->json([]);
        }

        $customers = Customer::where('status', 'active')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit($maxResults)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'value' => $customer->name,
                    'label' => $customer->name . ' (' . $customer->customer_type . ') - ' . ($customer->phone ?? 'No phone'),
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'customer_type' => $customer->customer_type,
                ];
            });

        return response()->json($customers);
    }

    /**
     * Select customer for shipment
     */
    public function select(Request $request, $id)
    {
        $customer = Customer::with(['senders.addresses', 'receivers'])
            ->where('status', 'active')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'customer_type' => $customer->customer_type,
                'senders' => $customer->senders->map(function ($sender) {
                    return [
                        'id' => $sender->id,
                        'name' => $sender->name,
                        'phone' => $sender->phone,
                        'email' => $sender->email,
                        'addresses' => $sender->addresses->map(function ($address) {
                            return [
                                'id' => $address->id,
                                'full_address' => $address->getFullAddress(),
                                'is_default' => $address->is_default,
                            ];
                        }),
                    ];
                }),
                'receivers' => $customer->receivers->map(function ($receiver) {
                    return [
                        'id' => $receiver->id,
                        'name' => $receiver->name,
                        'phone' => $receiver->phone,
                        'email' => $receiver->email,
                        'address' => $receiver->address ? [
                            'id' => $receiver->address->id,
                            'full_address' => $receiver->address->getFullAddress(),
                        ] : null,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Quick customer creation
     */
    public function quickAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'customer_type' => 'required|in:individual,business,corporate',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'customer_type' => $request->customer_type,
                'created_by_branch_id' => Auth::user()->branch_id,
                'created_by_user_id' => Auth::id(),
                'status' => 'active',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully.',
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'customer_type' => $customer->customer_type,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating customer: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Recent customers list
     */
    public function recent(Request $request)
    {
        $limit = config('customer.search.recent_limit');
        
        $recentCustomers = Customer::where('status', 'active')
            ->where('created_by_branch_id', Auth::user()->branch_id)
            ->with(['senders', 'branch'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'customer_type' => $customer->customer_type,
                    'senders_count' => $customer->senders->count(),
                    'created_at' => $customer->created_at->format('M d, Y'),
                    'branch_name' => $customer->branch->name ?? 'N/A',
                ];
            });

        return response()->json($recentCustomers);
    }

    /**
     * Get customer details for shipment form
     */
    public function getDetails($id)
    {
        $customer = Customer::with(['senders.addresses', 'receivers.address'])
            ->where('status', 'active')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'customer_type' => $customer->customer_type,
                'senders' => $customer->senders->map(function ($sender) {
                    return [
                        'id' => $sender->id,
                        'name' => $sender->name,
                        'phone' => $sender->phone,
                        'email' => $sender->email,
                        'addresses' => $sender->addresses->map(function ($address) {
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
                        }),
                    ];
                }),
                'receivers' => $customer->receivers->map(function ($receiver) {
                    return [
                        'id' => $receiver->id,
                        'name' => $receiver->name,
                        'phone' => $receiver->phone,
                        'email' => $receiver->email,
                        'address' => $receiver->address ? [
                            'id' => $receiver->address->id,
                            'street' => $receiver->address->street,
                            'city' => $receiver->address->city,
                            'state' => $receiver->address->state,
                            'postal_code' => $receiver->address->postal_code,
                            'country' => $receiver->address->country,
                            'full_address' => $receiver->address->getFullAddress(),
                        ] : null,
                    ];
                }),
            ],
        ]);
    }
} 