<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function index(Request $request)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $query = Order::with('client');

        if ($request->has('client_id') && $request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->has('created_at') && $request->filled('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }

        $itemsPerPage = (int) $request->input('itemsPerPage', 10);
        $orders = $query->paginate($itemsPerPage)->appends($request->query());

        return response()->json([
            'data' => $orders->items(),
            'pagination' => [
                'currentPage' => $orders->currentPage(),
                'totalItems' => $orders->total(),
                'itemsPerPage' => $orders->perPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $order = Order::create($validated);

        return response()->json(['message' => 'Order created', 'data' => $order], 201);
    }

    public function show(Order $order)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        return response()->json($order->load('client'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $order->update($validated);

        return response()->json(['message' => 'Order updated', 'data' => $order]);
    }

    public function destroy(Order $order)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }
}
