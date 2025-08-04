<?php

namespace Modules\Branch\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchMarkup;
use Modules\Shipper\Entities\Carrier;

/**
 * BranchSettingsController
 * Purpose: Branch admin manages their own branch settings only
 * Access: Branch Admin for own branch
 */
class BranchSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'role:branch_admin']);
    }

    /**
     * View current branch settings
     */
    public function index()
    {
        $branch = auth()->user()->branch;
        
        if (!$branch) {
            return redirect()->route('admin.dashboard.index')
                ->with('error', 'No branch assigned to your account.');
        }

        $branch->load(['markups.carrier']);
        
        return view('branch::settings.index', compact('branch'));
    }

    /**
     * Edit own branch settings
     */
    public function edit()
    {
        $branch = auth()->user()->branch;
        
        if (!$branch) {
            return redirect()->route('admin.dashboard.index')
                ->with('error', 'No branch assigned to your account.');
        }

        return view('branch::settings.edit', compact('branch'));
    }

    /**
     * Update own branch settings
     */
    public function update(Request $request)
    {
        $branch = auth()->user()->branch;
        
        if (!$branch) {
            return redirect()->route('admin.dashboard.index')
                ->with('error', 'No branch assigned to your account.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_person' => 'required|string|max:255',
            'operating_hours' => 'nullable|array',
            'operating_hours.*.open' => 'nullable|date_format:H:i',
            'operating_hours.*.close' => 'nullable|date_format:H:i',
            'settings' => 'nullable|array'
        ]);

        try {
            $branch->update($validated);

            return redirect()
                ->route('branch.settings.index')
                ->with('success', 'Branch settings updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update branch settings: ' . $e->getMessage());
        }
    }

    /**
     * Manage markup rules for carriers
     */
    public function markups()
    {
        $branch = auth()->user()->branch;
        
        if (!$branch) {
            return redirect()->route('admin.dashboard.index')
                ->with('error', 'No branch assigned to your account.');
        }

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

        return view('branch::settings.markups', compact(
            'branch',
            'carriers',
            'markupData'
        ));
    }

    /**
     * Update carrier markups
     */
    public function updateMarkups(Request $request)
    {
        $branch = auth()->user()->branch;
        
        if (!$branch) {
            return redirect()->route('admin.dashboard.index')
                ->with('error', 'No branch assigned to your account.');
        }

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
                ->route('branch.settings.markups')
                ->with('success', 'Markup rules updated successfully.');

        } catch (\Exception $e) {
            \DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update markup rules: ' . $e->getMessage());
        }
    }

    /**
     * Branch profile management
     */
    public function profile()
    {
        $branch = auth()->user()->branch;
        
        if (!$branch) {
            return redirect()->route('admin.dashboard.index')
                ->with('error', 'No branch assigned to your account.');
        }

        $branch->load(['creator', 'users']);

        return view('branch::settings.profile', compact('branch'));
    }
} 