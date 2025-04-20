<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends BaseController
{
    public function index(Request $request)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $query = MenuItem::query();

        if ($request->has('name') && $request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('price_min') && $request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }

        if ($request->has('price_max') && $request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        $itemsPerPage = (int) $request->input('itemsPerPage', 10);
        $menuItems = $query->paginate($itemsPerPage)->appends($request->query());

        return response()->json([
            'data' => $menuItems->items(),
            'pagination' => [
                'currentPage' => $menuItems->currentPage(),
                'totalItems' => $menuItems->total(),
                'itemsPerPage' => $menuItems->perPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $menuItem = MenuItem::create($validated);

        return response()->json(['message' => 'Menu item created', 'data' => $menuItem], 201);
    }

    public function show(MenuItem $menuItem)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        return response()->json($menuItem);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $menuItem->update($validated);

        return response()->json(['message' => 'Menu item updated', 'data' => $menuItem]);
    }

    public function destroy(MenuItem $menuItem)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $menuItem->delete();

        return response()->json(['message' => 'Menu item deleted']);
    }
}
