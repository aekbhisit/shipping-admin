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
use Modules\Customer\Entities\Address;
use Modules\Branch\Entities\Branch;
use Yajra\DataTables\Facades\DataTables;

class CustomerAdminController extends Controller
{
    /**
     * Display a listing of customers with search and filter
     */
    public function index(Request $request)
    {
        $branches = Branch::all();
        $customerTypes = config('customer.customer_types');

        return view('customer::admin.customers.index', compact('branches', 'customerTypes'));
    }

        /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        $branches = \Modules\Branch\Entities\Branch::all();
        $customerTypes = config('customer.customer_types');

        return view('customer::admin.customers.create', compact('branches', 'customerTypes'));
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), config('customer.validation.customer'));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $customerData = [
                'email' => $request->email,
                'phone' => $request->phone,
                'tax_id' => $request->tax_id,
                'customer_type' => $request->customer_type,
                'is_active' => true,
                'created_by_branch' => $request->branch_id ?? 2, // Default to branch ID 2
                'created_by_user' => Auth::id() ?? 1, // Default to user ID 1
            ];
            
            // Handle name fields based on customer type
            if ($request->customer_type === 'business' || $request->customer_type === 'corporate') {
                $customerData['company_name'] = $request->name;
            } else {
                $customerData['individual_name'] = $request->name;
            }
            
            $customer = Customer::create($customerData);

            DB::commit();

            return redirect()->route('admin.customers.show', $customer->id)
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error creating customer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified customer with details and history
     */
    public function show($id)
    {
        $customer = Customer::with(['senders.addresses', 'branch'])
            ->findOrFail($id);

        $senders = $customer->senders()->with('addresses')->get();
        $receivers = collect(); // Empty collection for now

        return view('customer::admin.customers.show', compact('customer', 'senders', 'receivers'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $branches = \Modules\Branch\Entities\Branch::all();
        $customerTypes = config('customer.customer_types');

        return view('customer::admin.customers.edit', compact('customer', 'branches', 'customerTypes'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $validator = Validator::make($request->all(), config('customer.validation.customer'));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'email' => $request->email,
                'phone' => $request->phone,
                'tax_id' => $request->tax_id,
                'customer_type' => $request->customer_type,
            ];
            
            // Handle name fields based on customer type
            if ($request->customer_type === 'business' || $request->customer_type === 'corporate') {
                $updateData['company_name'] = $request->name;
            } else {
                $updateData['individual_name'] = $request->name;
            }
            
            $customer->update($updateData);

            return redirect()->route('admin.customers.show', $customer->id)
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating customer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Deactivate the specified customer
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        try {
            $customer->update([
                'is_active' => false,
            ]);

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer deactivated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deactivating customer: ' . $e->getMessage());
        }
    }

    /**
     * AJAX customer search for autocomplete
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $minLength = config('customer.search.min_length');
        $maxResults = config('customer.search.max_results');

        if (strlen($search) < $minLength) {
            return response()->json([]);
        }

        $customers = Customer::where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('company_name', 'like', "%{$search}%")
                      ->orWhere('individual_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('tax_id', 'like', "%{$search}%");
            })
            ->with(['senders', 'branch'])
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
                    'senders_count' => $customer->senders->count(),
                    'branch_name' => $customer->branch->name ?? 'N/A',
                ];
            });

        return response()->json($customers);
    }

    /**
     * Merge duplicate customers
     */
    public function merge(Request $request)
    {
        $request->validate([
            'primary_customer_id' => 'required|exists:customers,id',
            'duplicate_customer_ids' => 'required|array',
            'duplicate_customer_ids.*' => 'exists:customers,id',
        ]);

        try {
            DB::beginTransaction();

            $primaryCustomer = Customer::findOrFail($request->primary_customer_id);
            $duplicateCustomers = Customer::whereIn('id', $request->duplicate_customer_ids)->get();

            foreach ($duplicateCustomers as $duplicate) {
                // Move senders to primary customer
                $duplicate->senders()->update(['customer_id' => $primaryCustomer->id]);
                
                // Move receivers to primary customer
                $duplicate->receivers()->update(['customer_id' => $primaryCustomer->id]);
                
                // Deactivate duplicate customer
                $duplicate->update(['status' => 'merged']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customers merged successfully.',
                'primary_customer_id' => $primaryCustomer->id,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error merging customers: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customers for DataTables
     */
    public function getDataTable(Request $request)
    {
        $customers = Customer::with(['senders', 'branch'])
            ->select('customers.*');

        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('name', function ($customer) {
                return $customer->name; // Uses the accessor we just created
            })
            ->addColumn('senders_count', function ($customer) {
                return $customer->senders->count();
            })
            ->addColumn('branch_name', function ($customer) {
                return $customer->branch->name ?? 'N/A';
            })
            ->addColumn('customer_type', function ($customer) {
                $badgeClass = match($customer->customer_type) {
                    'business' => 'bg-primary',
                    'corporate' => 'bg-success',
                    'individual' => 'bg-info',
                    default => 'bg-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($customer->customer_type) . '</span>';
            })
            ->addColumn('status', function ($customer) {
                $badgeClass = $customer->status == 'active' ? 'bg-success' : 'bg-danger';
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($customer->status) . '</span>';
            })
            ->addColumn('actions', function ($customer) {
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('admin.customers.show', $customer->id) . '" class="btn btn-sm btn-info me-1" title="View"><i class="bx bx-show"></i></a>';
                $actions .= '<a href="' . route('admin.customers.edit', $customer->id) . '" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bx bx-edit"></i></a>';
                if ($customer->status == 'active') {
                    $actions .= '<form method="POST" action="' . route('admin.customers.destroy', $customer->id) . '" style="display: inline;" onsubmit="return confirm(\'Are you sure you want to deactivate this customer?\')">';
                    $actions .= csrf_field();
                    $actions .= method_field('DELETE');
                    $actions .= '<button type="submit" class="btn btn-sm btn-danger" title="Deactivate"><i class="bx bx-trash"></i></button>';
                    $actions .= '</form>';
                }
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['customer_type', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Export customers
     */
    public function export(Request $request)
    {
        $customers = Customer::with(['senders', 'branch'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                      ->orWhere('individual_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('tax_id', 'like', "%{$search}%");
                });
            })
            ->when($request->customer_type, function ($query, $type) {
                $query->where('customer_type', $type);
            })
            ->when($request->branch_id, function ($query, $branchId) {
                $query->where('created_by_branch', $branchId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'customers_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Type', 'Status', 'Branch', 'Senders Count', 'Created At']);
            
            // Add data
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->name, // Uses the accessor
                    $customer->email ?? 'N/A',
                    $customer->phone ?? 'N/A',
                    ucfirst($customer->customer_type),
                    ucfirst($customer->status), // Uses the accessor
                    $customer->branch->name ?? 'N/A',
                    $customer->senders->count(),
                    $customer->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 