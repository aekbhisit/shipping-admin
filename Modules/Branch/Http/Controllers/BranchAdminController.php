<?php

namespace Modules\Branch\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchMarkup;
use Modules\Branch\Services\BranchService;
use Modules\Shipper\Entities\Carrier;

/**
 * BranchAdminController
 * Purpose: Company admin manages all branches and configurations
 * Access: Company Admin only
 */
class BranchAdminController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->middleware(['auth:admin', 'role:company_admin']);
        $this->branchService = $branchService;
    }

    /**
     * Display a listing of branches with multiple view options and filtering
     */
    public function index(Request $request)
    {
        $query = Branch::with(['creator', 'markups.carrier']);

        // Apply filters
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Get branches with pagination
        $branches = $query->paginate(15);

        // Get branch statistics for each branch
        $branches->getCollection()->transform(function ($branch) {
            $branch->stats = $branch->getStats();
            $branch->performance = $branch->getPerformanceMetrics();
            return $branch;
        });

        // Get summary statistics
        $totalBranches = Branch::count();
        $activeBranches = Branch::active()->count();
        $totalUsers = \DB::table('users')->whereNotNull('branch_id')->count();
        $totalMarkups = BranchMarkup::active()->count();

        return view('branch::admin.branches.index', compact(
            'branches',
            'totalBranches',
            'activeBranches', 
            'totalUsers',
            'totalMarkups'
        ));
    }

    /**
     * Show the form for creating a new branch
     */
    public function create()
    {
        return view('branch::admin.branches.create');
    }

    /**
     * Store a newly created branch
     */
    public function store(Request $request)
    {
        // Validation with required fields and format validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_person' => 'required|string|max:255',
            'operating_hours' => 'nullable|array',
            'operating_days' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        // Handle operating_hours from form - convert to proper JSON structure
        $operatingHours = [];
        $operatingDays = $request->input('operating_days', []);
        $operatingHoursData = $request->input('operating_hours', []);
        
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            if (isset($operatingDays[$day]) && isset($operatingHoursData[$day])) {
                $open = $operatingHoursData[$day]['open'] ?? null;
                $close = $operatingHoursData[$day]['close'] ?? null;
                
                if ($open && $close) {
                    $operatingHours[$day] = [
                        'open' => $open,
                        'close' => $close
                    ];
                }
            }
        }
        
        $validated['operating_hours'] = $operatingHours;

        try {
            $branch = $this->branchService->createBranch(array_merge($validated, [
                'created_by' => auth()->id(),
                'is_active' => $request->boolean('is_active', true)
            ]));

            return redirect()
                ->route('admin.branches.show', $branch)
                ->with('success', 'Branch created successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create branch: ' . $e->getMessage());
        }
    }

    /**
     * Display branch details and performance
     */
    public function show(Branch $branch)
    {
        $branch->load(['creator', 'markups.carrier', 'users', 'reports']);
        
        // Get branch statistics
        $stats = $branch->getStats();
        
        // Get performance metrics for last 30 days
        $performance = $branch->getPerformanceMetrics();
        
        // Get recent reports
        $recentReports = $branch->reports()
            ->recent(30)
            ->orderBy('report_date', 'desc')
            ->take(10)
            ->get();

        // Get active markups
        $markups = $branch->markups()
            ->with('carrier')
            ->active()
            ->get();

        return view('branch::admin.branches.show', compact(
            'branch',
            'stats',
            'performance',
            'recentReports',
            'markups'
        ));
    }

    /**
     * Show the form for editing branch information
     */
    public function edit(Branch $branch)
    {
        return view('branch::admin.branches.edit', compact('branch'));
    }

    /**
     * Update branch data with validation and audit trail
     */
    public function update(Request $request, Branch $branch)
    {
        // Validation with required fields and format validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_person' => 'required|string|max:255',
            'operating_hours' => 'nullable|array',
            'operating_days' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        // Handle operating_hours from form - convert to proper JSON structure
        $operatingHours = [];
        $operatingDays = $request->input('operating_days', []);
        $operatingHoursData = $request->input('operating_hours', []);
        
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            if (isset($operatingDays[$day]) && isset($operatingHoursData[$day])) {
                $open = $operatingHoursData[$day]['open'] ?? null;
                $close = $operatingHoursData[$day]['close'] ?? null;
                
                if ($open && $close) {
                    $operatingHours[$day] = [
                        'open' => $open,
                        'close' => $close
                    ];
                }
            }
        }
        
        $validated['operating_hours'] = $operatingHours;

        try {
            // Log changes for audit trail
            $oldData = $branch->toArray();
            
            $branch->update($validated);

            // Log the change (implement audit logging as needed)
            \Log::info('Branch updated', [
                'branch_id' => $branch->id,
                'user_id' => auth()->id(),
                'old_data' => $oldData,
                'new_data' => $validated
            ]);

            return redirect()
                ->route('admin.branches.show', $branch)
                ->with('success', 'Branch updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update branch: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate branch (soft delete using simple active/inactive status)
     */
    public function destroy(Branch $branch)
    {
        try {
            // Check if branch has active users or recent shipments
            $activeUsers = $branch->users()->where('is_active', true)->count();
            // TODO: Uncomment when Shipment module is implemented
            // $recentShipments = $branch->shipments()->where('created_at', '>=', now()->subDays(30))->count();
            $recentShipments = 0; // Temporary: Set to 0 until Shipment module is implemented

            if ($activeUsers > 0) {
                return back()->with('error', 'Cannot deactivate branch with active users. Please deactivate users first.');
            }

            if ($recentShipments > 0) {
                return back()->with('error', 'Cannot deactivate branch with recent shipments. Please wait 30 days after last shipment.');
            }

            // Deactivate the branch
            $branch->update(['is_active' => false]);

            // Deactivate all markups
            $branch->markups()->update(['is_active' => false]);

            // Log the deactivation
            \Log::info('Branch deactivated', [
                'branch_id' => $branch->id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.branches.index')
                ->with('success', 'Branch deactivated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deactivate branch: ' . $e->getMessage());
        }
    }

    /**
     * View branch markup rules with grid interface
     */
    public function viewMarkups(Branch $branch)
    {
        // Get all carriers
        $carriers = Carrier::active()->get();
        
        // Get existing markups for this branch
        $markups = $branch->markups()
            ->with('carrier')
            ->get()
            ->keyBy('carrier_id');

        // Create markup grid data
        $markupData = $carriers->map(function ($carrier) use ($markups) {
            $markup = $markups->get($carrier->id);
            
            return [
                'carrier' => $carrier,
                'markup' => $markup,
                'has_markup' => !is_null($markup),
                'percentage' => $markup ? $markup->markup_percentage : 0,
                'min_amount' => $markup ? $markup->min_markup_amount : 0,
                'max_percentage' => $markup ? $markup->max_markup_percentage : 100,
                'is_active' => $markup ? $markup->is_active : false
            ];
        });

        return view('branch::admin.branches.markups', compact(
            'branch',
            'carriers',
            'markupData'
        ));
    }

    /**
     * Update markup rules in bulk
     */
    public function updateMarkups(Request $request, Branch $branch)
    {
        // Validate markup data
        $validated = $request->validate([
            'markups' => 'required|array',
            'markups.*.carrier_id' => 'required|exists:carriers,id',
            'markups.*.markup_percentage' => 'required|numeric|min:0|max:100',
            'markups.*.min_markup_amount' => 'nullable|numeric|min:0',
            'markups.*.max_markup_percentage' => 'required|numeric|min:0|max:100',
            'markups.*.is_active' => 'boolean'
        ]);

        try {
            \DB::beginTransaction();

            foreach ($validated['markups'] as $markupData) {
                // Validate markup limits
                if ($markupData['markup_percentage'] > $markupData['max_markup_percentage']) {
                    throw new \InvalidArgumentException('Markup percentage cannot exceed maximum markup percentage');
                }

                BranchMarkup::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'carrier_id' => $markupData['carrier_id']
                    ],
                    [
                        'markup_percentage' => $markupData['markup_percentage'],
                        'min_markup_amount' => $markupData['min_markup_amount'] ?? 0,
                        'max_markup_percentage' => $markupData['max_markup_percentage'],
                        'is_active' => $markupData['is_active'] ?? true,
                        'updated_by' => auth()->id()
                    ]
                );
            }

            \DB::commit();

            return redirect()
                ->route('admin.branches.markups', $branch)
                ->with('success', 'Markup rules updated successfully.');

        } catch (\Exception $e) {
            \DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update markup rules: ' . $e->getMessage());
        }
    }

    /**
     * Activate branch
     */
    public function activate(Branch $branch)
    {
        try {
            $branch->update(['is_active' => true]);

            return back()->with('success', 'Branch activated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate branch: ' . $e->getMessage());
        }
    }

    /**
     * Get branch data for AJAX calls
     */
    public function getBranchData(Branch $branch)
    {
        return response()->json([
            'branch' => $branch,
            'stats' => $branch->getStats(),
            'performance' => $branch->getPerformanceMetrics(),
            'markups' => $branch->markups()->with('carrier')->get()
        ]);
    }

    /**
     * DataTable AJAX for branches listing
     */
    public function datatable_ajax(Request $request)
    {
        try {
            $query = Branch::with(['creator', 'markups.carrier']);

            // Apply filters
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            // Handle DataTable search parameter
            if ($request->filled('search') && is_array($request->search) && isset($request->search['value'])) {
                $searchValue = $request->search['value'];
                if (!empty($searchValue)) {
                    $query->search($searchValue);
                }
            } elseif ($request->filled('search') && is_string($request->search)) {
                // Handle direct string search (for non-DataTable requests)
                $query->search($request->search);
            }

            // Get total count before pagination
            $totalRecords = $query->count();
            
            // Apply DataTable ordering
            if ($request->filled('order') && is_array($request->order)) {
                foreach ($request->order as $order) {
                    $columnIndex = $order['column'] ?? 0;
                    $direction = $order['dir'] ?? 'asc';
                    
                    // Map DataTable column index to actual database column
                    $columnMap = [
                        0 => 'id',           // #
                        1 => 'name',         // Branch Name
                        2 => 'code',         // Code
                        3 => 'contact_person', // Contact
                        4 => 'id',           // Users (no direct column)
                        5 => 'id',           // Performance (no direct column)
                        6 => 'id',           // Markups (no direct column)
                        7 => 'is_active',    // Status
                        8 => 'id'            // Actions (no direct column)
                    ];
                    
                    $column = $columnMap[$columnIndex] ?? 'id';
                    
                    // Only order by actual database columns
                    if (in_array($column, ['id', 'name', 'code', 'contact_person', 'is_active'])) {
                        $query->orderBy($column, $direction);
                    }
                }
            } else {
                // Default ordering
                $query->orderBy('name', 'asc');
            }
            
            // Apply DataTable pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            
            $branches = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $branches->map(function ($branch, $index) {
                    try {
                        $stats = $branch->getStats();
                        $performance = $branch->getPerformanceMetrics();
                        
                        return [
                            'DT_RowIndex' => $index + 1,
                            'name' => '<strong>' . $branch->name . '</strong><br><small class="text-muted">' . $branch->short_address . '</small>',
                            'code' => '<span class="badge bg-secondary">' . $branch->code . '</span>',
                            'contact_person' => '<i class="bx bx-user"></i> ' . $branch->contact_person . '<br><i class="bx bx-phone"></i> ' . $branch->formatted_phone,
                            'users_count' => '<span class="badge bg-info">' . $stats['active_users'] . '</span> Active<br><span class="badge bg-light text-dark">' . $stats['total_users'] . '</span> Total',
                            'performance' => '<strong>฿' . number_format($performance['total_revenue'], 0) . '</strong><br><small class="text-muted">' . $performance['total_shipments'] . ' shipments</small>',
                            'markups_count' => '<span class="badge bg-primary">' . $stats['active_markups'] . '</span> Active<br><span class="badge bg-light text-dark">' . $stats['total_markups'] . '</span> Total',
                            'status' => '<span class="' . $branch->status_badge . '">' . $branch->status_text . '</span>',
                            'actions' => $this->getActionButtons($branch)
                        ];
                    } catch (\Exception $e) {
                        \Log::error('Error processing branch for DataTable: ' . $e->getMessage(), [
                            'branch_id' => $branch->id,
                            'error' => $e->getMessage()
                        ]);
                        
                        return [
                            'DT_RowIndex' => $index + 1,
                            'name' => '<strong>' . $branch->name . '</strong><br><small class="text-muted">Error loading data</small>',
                            'code' => '<span class="badge bg-secondary">' . $branch->code . '</span>',
                            'contact_person' => 'Error loading contact',
                            'users_count' => 'Error loading users',
                            'performance' => 'Error loading performance',
                            'markups_count' => 'Error loading markups',
                            'status' => '<span class="' . $branch->status_badge . '">' . $branch->status_text . '</span>',
                            'actions' => '<span class="text-muted">Error loading actions</span>'
                        ];
                    }
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('DataTable AJAX error: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while loading the data.'
            ], 500);
        }
    }

    /**
     * Generate action buttons for DataTable
     */
    private function getActionButtons(Branch $branch)
    {
        try {
            $buttons = '';
            
            $buttons .= '<a href="' . route('admin.branches.show', $branch) . '" class="btn btn-sm btn-outline-primary me-1" title="View"><i class="bx bx-show"></i></a>';
            $buttons .= '<a href="' . route('admin.branches.edit', $branch) . '" class="btn btn-sm btn-outline-warning me-1" title="Edit"><i class="bx bx-edit"></i></a>';
            $buttons .= '<a href="' . route('admin.branches.markups', $branch) . '" class="btn btn-sm btn-outline-info me-1" title="Markups"><i class="bx bx-dollar-circle"></i></a>';
            
            if ($branch->is_active) {
                $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deactivateBranch(' . $branch->id . ')" title="Deactivate"><i class="bx bx-x-circle"></i></button>';
            } else {
                $buttons .= '<form method="POST" action="' . route('admin.branches.activate', $branch) . '" style="display: inline;"><input type="hidden" name="_token" value="' . csrf_token() . '"><button type="submit" class="btn btn-sm btn-outline-success" title="Activate"><i class="bx bx-check-circle"></i></button></form>';
            }
            
            return $buttons;
        } catch (\Exception $e) {
            \Log::error('Error generating action buttons: ' . $e->getMessage(), [
                'branch_id' => $branch->id,
                'error' => $e->getMessage()
            ]);
            
            return '<span class="text-muted">Error loading actions</span>';
        }
    }

    /**
     * Export branches data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $branches = Branch::with(['creator', 'markups.carrier'])
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->get();

        if ($format === 'csv') {
            return $this->exportToCsv($branches);
        }

        return back()->with('error', 'Unsupported export format.');
    }

    /**
     * Export branches to CSV
     */
    private function exportToCsv($branches)
    {
        $filename = 'branches_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($branches) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Code', 'Address', 'Phone', 'Email', 
                'Contact Person', 'Status', 'Created By', 'Created At'
            ]);

            // CSV data
            foreach ($branches as $branch) {
                fputcsv($file, [
                    $branch->id,
                    $branch->name,
                    $branch->code,
                    $branch->address,
                    $branch->phone,
                    $branch->email,
                    $branch->contact_person,
                    $branch->is_active ? 'Active' : 'Inactive',
                    $branch->creator?->name ?? 'Unknown',
                    $branch->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 