<?php

namespace Modules\Favorites\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Favorites\Entities\Favorite;
use Modules\Core\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

class FavoritesAdminController extends AdminController
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display user's favorite shipments
     */
    public function shipments(Request $request)
    {
        $adminInit = $this->adminInit();
        
        $favorites = Favorite::with(['favorable', 'user'])
            ->where('user_id', Auth::id())
            ->favorableType('Modules\Shipment\Entities\Shipment')
            ->ofType('like')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('favorites::admin.shipments', compact('favorites', 'adminInit'));
    }

    /**
     * Display user's favorite customers
     */
    public function customers(Request $request)
    {
        $adminInit = $this->adminInit();
        
        $favorites = Favorite::with(['favorable', 'user'])
            ->where('user_id', Auth::id())
            ->favorableType('Modules\Customer\Entities\Customer')
            ->ofType('like')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('favorites::admin.customers', compact('favorites', 'adminInit'));
    }

    /**
     * Display user's favorite products
     */
    public function products(Request $request)
    {
        $adminInit = $this->adminInit();
        
        $favorites = Favorite::with(['favorable', 'user'])
            ->where('user_id', Auth::id())
            ->favorableType('Modules\Product\Entities\Product')
            ->ofType('like')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('favorites::admin.products', compact('favorites', 'adminInit'));
    }

    /**
     * Display user's saved items
     */
    public function saved(Request $request)
    {
        $adminInit = $this->adminInit();
        
        $favorites = Favorite::with(['favorable', 'user'])
            ->where('user_id', Auth::id())
            ->ofType('bookmark')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('favorites::admin.saved', compact('favorites', 'adminInit'));
    }

    /**
     * Toggle favorite status for an item
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'favorable_type' => 'required|string',
            'favorable_id' => 'required|integer',
            'favorite_type' => 'string|in:like,bookmark,star',
        ]);

        $userId = Auth::id();
        $favorableType = $request->favorable_type;
        $favorableId = $request->favorable_id;
        $favoriteType = $request->favorite_type ?? 'like';

        // Check if already favorited
        $existing = Favorite::where([
            'user_id' => $userId,
            'favorable_type' => $favorableType,
            'favorable_id' => $favorableId,
            'favorite_type' => $favoriteType,
        ])->first();

        if ($existing) {
            // Remove favorite
            $existing->delete();
            $action = 'removed';
        } else {
            // Add favorite
            Favorite::create([
                'user_id' => $userId,
                'favorable_type' => $favorableType,
                'favorable_id' => $favorableId,
                'favorite_type' => $favoriteType,
                'notes' => $request->notes,
            ]);
            $action = 'added';
        }

        return response()->json([
            'success' => true,
            'action' => $action,
            'favorite_count' => Favorite::getFavoriteCount($favorableType, $favorableId, $favoriteType),
        ]);
    }

    /**
     * Remove a favorite
     */
    public function remove(Request $request, $id)
    {
        $favorite = Favorite::where('user_id', Auth::id())->findOrFail($id);
        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Favorite removed successfully',
        ]);
    }

    /**
     * Update favorite notes
     */
    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $favorite = Favorite::where('user_id', Auth::id())->findOrFail($id);
        $favorite->update(['notes' => $request->notes]);

        return response()->json([
            'success' => true,
            'message' => 'Notes updated successfully',
        ]);
    }
} 