<?php

namespace Modules\Customer\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\Sender;
use Modules\Customer\Entities\Receiver;
use Modules\Customer\Entities\Address;

class CustomerService
{
    /**
     * Create a new customer with validation
     */
    public function createCustomer(array $data)
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'customer_type' => $data['customer_type'],
                'created_by_branch_id' => Auth::user()->branch_id,
                'created_by_user_id' => Auth::id(),
                'status' => 'active',
            ]);

            return $customer;
        });
    }

    /**
     * Update customer information
     */
    public function updateCustomer(Customer $customer, array $data)
    {
        return DB::transaction(function () use ($customer, $data) {
            $customer->update([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'customer_type' => $data['customer_type'],
                'updated_by_user_id' => Auth::id(),
            ]);

            return $customer;
        });
    }

    /**
     * Search customers with filters
     */
    public function searchCustomers(array $filters = [])
    {
        $query = Customer::with(['senders', 'branch']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%");
            });
        }

        // Apply customer type filter
        if (!empty($filters['customer_type'])) {
            $query->where('customer_type', $filters['customer_type']);
        }

        // Apply branch filter
        if (!empty($filters['branch_id'])) {
            $query->where('created_by_branch_id', $filters['branch_id']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get recent customers for a branch
     */
    public function getRecentCustomers($branchId = null, $limit = 10)
    {
        $query = Customer::where('status', 'active')
            ->with(['senders', 'branch']);

        if ($branchId) {
            $query->where('created_by_branch_id', $branchId);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Merge duplicate customers
     */
    public function mergeCustomers($primaryCustomerId, array $duplicateCustomerIds)
    {
        return DB::transaction(function () use ($primaryCustomerId, $duplicateCustomerIds) {
            $primaryCustomer = Customer::findOrFail($primaryCustomerId);
            $duplicateCustomers = Customer::whereIn('id', $duplicateCustomerIds)->get();

            foreach ($duplicateCustomers as $duplicate) {
                // Move senders to primary customer
                $duplicate->senders()->update(['customer_id' => $primaryCustomer->id]);
                
                // Move receivers to primary customer
                $duplicate->receivers()->update(['customer_id' => $primaryCustomer->id]);
                
                // Deactivate duplicate customer
                $duplicate->update(['status' => 'merged']);
            }

            return $primaryCustomer;
        });
    }

    /**
     * Check for potential duplicate customers
     */
    public function findPotentialDuplicates(Customer $customer)
    {
        return Customer::where('id', '!=', $customer->id)
            ->where('status', 'active')
            ->where(function ($query) use ($customer) {
                $query->where('name', $customer->name)
                      ->orWhere('email', $customer->email)
                      ->orWhere('phone', $customer->phone)
                      ->orWhere('tax_id', $customer->tax_id);
            })
            ->get();
    }

    /**
     * Get customer statistics
     */
    public function getCustomerStats($branchId = null)
    {
        $query = Customer::where('status', 'active');

        if ($branchId) {
            $query->where('created_by_branch_id', $branchId);
        }

        $totalCustomers = $query->count();
        $customersByType = $query->selectRaw('customer_type, COUNT(*) as count')
            ->groupBy('customer_type')
            ->pluck('count', 'customer_type')
            ->toArray();

        $recentCustomers = $query->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_customers' => $totalCustomers,
            'customers_by_type' => $customersByType,
            'recent_customers' => $recentCustomers,
        ];
    }

    /**
     * Create sender for customer
     */
    public function createSender(Customer $customer, array $data)
    {
        return DB::transaction(function () use ($customer, $data) {
            $sender = Sender::create([
                'customer_id' => $customer->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'created_by_user_id' => Auth::id(),
                'status' => 'active',
            ]);

            // Create address if provided
            if (!empty($data['address'])) {
                Address::create([
                    'sender_id' => $sender->id,
                    'street' => $data['address']['street'],
                    'city' => $data['address']['city'],
                    'state' => $data['address']['state'],
                    'postal_code' => $data['address']['postal_code'],
                    'country' => $data['address']['country'],
                    'is_default' => true,
                    'created_by_user_id' => Auth::id(),
                ]);
            }

            return $sender;
        });
    }

    /**
     * Create receiver for customer
     */
    public function createReceiver(Customer $customer, array $data)
    {
        return DB::transaction(function () use ($customer, $data) {
            $receiver = Receiver::create([
                'customer_id' => $customer->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'created_by_user_id' => Auth::id(),
                'status' => 'active',
            ]);

            // Create address if provided
            if (!empty($data['address'])) {
                Address::create([
                    'receiver_id' => $receiver->id,
                    'street' => $data['address']['street'],
                    'city' => $data['address']['city'],
                    'state' => $data['address']['state'],
                    'postal_code' => $data['address']['postal_code'],
                    'country' => $data['address']['country'],
                    'is_default' => true,
                    'created_by_user_id' => Auth::id(),
                ]);
            }

            return $receiver;
        });
    }

    /**
     * Get customer with all related data
     */
    public function getCustomerWithDetails($customerId)
    {
        return Customer::with([
            'senders.addresses',
            'receivers.address',
            'branch',
            'createdByUser',
            'updatedByUser'
        ])->findOrFail($customerId);
    }

    /**
     * Validate customer data
     */
    public function validateCustomerData(array $data)
    {
        $rules = config('customer.validation.customer');
        
        return validator($data, $rules);
    }

    /**
     * Check if customer exists by unique fields
     */
    public function customerExists(array $data)
    {
        $query = Customer::where('status', 'active');

        if (!empty($data['email'])) {
            $query->orWhere('email', $data['email']);
        }

        if (!empty($data['phone'])) {
            $query->orWhere('phone', $data['phone']);
        }

        if (!empty($data['tax_id'])) {
            $query->orWhere('tax_id', $data['tax_id']);
        }

        return $query->exists();
    }
} 