<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends BaseController
{
    public function index(Request $request)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $query = OrderItem::with(['order.client', 'menuItem']);

        if ($request->has('order_id') && $request->filled('order_id')) {
            $query->where('order_id', $request->input('order_id'));
        }

        if ($request->has('menu_item_id') && $request->filled('menu_item_id')) {
            $query->where('menu_item_id', $request->input('menu_item_id'));
        }

        if ($request->has('quantity_min') && $request->filled('quantity_min')) {
            $query->where('quantity', '>=', $request->input('quantity_min'));
        }

        if ($request->has('quantity_max') && $request->filled('quantity_max')) {
            $query->where('quantity', '<=', $request->input('quantity_max'));
        }

        $itemsPerPage = (int) $request->input('itemsPerPage', 10);
        $orderItems = $query->paginate($itemsPerPage)->appends($request->query());

        return response()->json([
            'data' => $orderItems->items(),
            'pagination' => [
                'currentPage' => $orderItems->currentPage(),
                'totalItems' => $orderItems->total(),
                'itemsPerPage' => $orderItems->perPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $orderItem = OrderItem::create($validated);

        return response()->json(['message' => 'Order item created', 'data' => $orderItem], 201);
    }

    public function show(OrderItem $orderItem)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        return response()->json($orderItem->load(['order.client', 'menuItem']));
    }

    public function update(Request $request, OrderItem $orderItem)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $orderItem->update($validated);

        return response()->json(['message' => 'Order item updated', 'data' => $orderItem]);
    }

    public function destroy(OrderItem $orderItem)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $orderItem->delete();

        return response()->json(['message' => 'Order item deleted']);
    }
}
