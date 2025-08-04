<?php

namespace Modules\Shipper\Repositories\Eloquent;

use Modules\Shipper\Repositories\Contracts\CarrierRepositoryInterface;
use Modules\Shipper\Entities\Carrier;
use Yajra\DataTables\DataTables;

class CarrierRepository implements CarrierRepositoryInterface
{
    /**
     * Get all carriers
     */
    public function all()
    {
        return Carrier::all();
    }

    /**
     * Get all active carriers ordered by priority
     */
    public function getActiveByPriority()
    {
        return Carrier::active()->byPriority()->get();
    }

    /**
     * Find carrier by ID
     */
    public function find($id)
    {
        return Carrier::find($id);
    }

    /**
     * Find carrier by code
     */
    public function findByCode($code)
    {
        return Carrier::where('code', $code)->first();
    }

    /**
     * Create new carrier
     */
    public function create(array $data)
    {
        return Carrier::create($data);
    }

    /**
     * Update carrier
     */
    public function update($id, array $data)
    {
        $carrier = Carrier::find($id);
        if ($carrier) {
            return $carrier->update($data);
        }
        return false;
    }

    /**
     * Delete carrier
     */
    public function delete($id)
    {
        $carrier = Carrier::find($id);
        if ($carrier) {
            return $carrier->delete();
        }
        return false;
    }

    /**
     * Get carriers with active configurations for branch
     */
    public function getWithActiveConfiguration($branchId = null)
    {
        return Carrier::active()
            ->whereHas('carrierCredentials', function($query) use ($branchId) {
                $query->where('is_active', true);
                if ($branchId) {
                    $query->where(function($q) use ($branchId) {
                        $q->where('branch_id', $branchId)
                          ->orWhereNull('branch_id');
                    });
                } else {
                    $query->whereNull('branch_id');
                }
            })
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get carrier API success rate
     */
    public function getSuccessRate($carrierId, $hours = 24)
    {
        // For now, return null as success rate tracking is not implemented yet
        return null;
    }

    /**
     * Update carrier status
     */
    public function updateStatus($id, $isActive)
    {
        return Carrier::where('id', $id)->update(['is_active' => $isActive]);
    }

    /**
     * Get carriers for DataTable
     */
    public function getForDataTable($request)
    {
        try {
            $carriers = Carrier::query();
            
            // Debug: Check if we have any carriers
            $count = $carriers->count();
            
            return DataTables::of($carriers)
            ->addColumn('logo', function($carrier) {
                if ($carrier->logo_url) {
                    return '<img src="' . $carrier->logo_url . '" class="img-thumbnail" style="max-width: 50px; height: 50px; object-fit: cover;" alt="' . $carrier->name . '">';
                }
                return '<div class="logo-placeholder"><i class="bx bx-image"></i></div>';
            })
            ->addColumn('name', function($carrier) {
                return $carrier->name;
            })
            ->addColumn('code', function($carrier) {
                return '<span class="badge bg-primary text-white">' . $carrier->code . '</span>';
            })
            ->addColumn('api_endpoint', function($carrier) {
                return '<code class="text-primary">' . \Str::limit($carrier->api_base_url, 40) . '</code>';
            })
            ->addColumn('supported_services', function($carrier) {
                $services = $carrier->getSupportedServices();
                if (!empty($services)) {
                    $html = '';
                    foreach ($services as $service) {
                        $html .= '<span class="badge bg-success text-white service-badge me-1">' . $service . '</span>';
                    }
                    return $html;
                }
                return '<span class="text-muted fst-italic">No services</span>';
            })
            ->addColumn('status', function($carrier) {
                if ($carrier->is_active) {
                    return '<span class="badge bg-success text-white">Active</span>';
                }
                return '<span class="badge bg-secondary text-white">Inactive</span>';
            })
            ->addColumn('tools', function($carrier) {
                $tools = '';
                
                // Test Connection Tool
                $tools .= '<button type="button" class="btn btn-sm btn-outline-info me-1" title="Test API Connection" onclick="testConnection(' . $carrier->id . ')">';
                $tools .= '<i class="bx bx-wifi"></i>';
                $tools .= '</button>';
                
                // View Logs Tool
                $tools .= '<a href="/admin/shippers/' . $carrier->id . '/logs" class="btn btn-sm btn-outline-warning me-1" title="View API Logs">';
                $tools .= '<i class="bx bx-history"></i>';
                $tools .= '</a>';
                
                // Configuration Tool
                $tools .= '<button type="button" class="btn btn-sm btn-outline-secondary me-1" title="Manage Configuration" onclick="manageConfig(' . $carrier->id . ')">';
                $tools .= '<i class="bx bx-cog"></i>';
                $tools .= '</button>';
                
                // Statistics Tool
                $tools .= '<button type="button" class="btn btn-sm btn-outline-success" title="View Statistics" onclick="viewStats(' . $carrier->id . ')">';
                $tools .= '<i class="bx bx-bar-chart-alt-2"></i>';
                $tools .= '</button>';
                
                return $tools;
            })
            ->addColumn('actions', function($carrier) {
                try {
                    $buttons = '';
                    
                    // View button - same as Branch module
                    $buttons .= '<a href="' . route('admin.shippers.show', $carrier) . '" class="btn btn-sm btn-outline-primary me-1" title="View"><i class="bx bx-show"></i></a>';
                    
                    // Edit button - same as Branch module
                    $buttons .= '<a href="' . route('admin.shippers.edit', $carrier) . '" class="btn btn-sm btn-outline-warning me-1" title="Edit"><i class="bx bx-edit"></i></a>';
                    
                    // Configuration button (similar to Branch's Markups button)
                    $buttons .= '<a href="/admin/shippers/' . $carrier->id . '/config" class="btn btn-sm btn-outline-info me-1" title="Configuration"><i class="bx bx-cog"></i></a>';
                    
                    // Status toggle - same pattern as Branch module
                    if ($carrier->is_active) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deactivateCarrier(' . $carrier->id . ')" title="Deactivate"><i class="bx bx-x-circle"></i></button>';
                    } else {
                        $buttons .= '<form method="POST" action="' . route('admin.shippers.activate', $carrier) . '" style="display: inline;"><input type="hidden" name="_token" value="' . csrf_token() . '"><button type="submit" class="btn btn-sm btn-outline-success" title="Activate"><i class="bx bx-check-circle"></i></button></form>';
                    }
                    
                    return $buttons;
                } catch (\Exception $e) {
                    \Log::error('Error generating action buttons: ' . $e->getMessage(), [
                        'carrier_id' => $carrier->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return '<span class="text-muted">Error loading actions</span>';
                }
            })
            ->rawColumns(['logo', 'code', 'api_endpoint', 'supported_services', 'status', 'tools', 'actions'])
            ->make(true);
        } catch (\Exception $e) {
            \Log::error('DataTable Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'DataTable processing error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update priority order
     */
    public function bulkUpdatePriority(array $priorities)
    {
        foreach ($priorities as $id => $priority) {
            Carrier::where('id', $id)->update(['priority_order' => $priority]);
        }
        return true;
    }

    /**
     * Get carrier statistics
     */
    public function getStatistics($carrierId = null)
    {
        $query = $carrierId ? Carrier::where('id', $carrierId) : Carrier::query();
        
        return [
            'total_carriers' => $carrierId ? 1 : Carrier::count(),
            'active_carriers' => $query->where('is_active', true)->count(),
            'configured_carriers' => $query->whereHas('carrierCredentials', function($q) {
                $q->where('is_active', true);
            })->count(),
            'recent_api_calls' => 0, // Will be implemented when API logs are added
            'success_rate' => $this->getAverageSuccessRate($carrierId)
        ];
    }

    /**
     * Get average success rate
     */
    private function getAverageSuccessRate($carrierId = null)
    {
        // For now, return null as success rate tracking is not implemented yet
        return null;
    }
} 