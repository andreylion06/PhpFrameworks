<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends BaseController
{
    public function index(Request $request)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $query = Client::query();

        if ($request->has('name') && $request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('phone') && $request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        $itemsPerPage = (int) $request->input('itemsPerPage', 10);
        $clients = $query->paginate($itemsPerPage)->appends($request->query());

        return response()->json([
            'data' => $clients->items(),
            'pagination' => [
                'currentPage' => $clients->currentPage(),
                'totalItems' => $clients->total(),
                'itemsPerPage' => $clients->perPage(),
            ],
        ]);
    }


    public function store(Request $request)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);

        $client = Client::create($validated);

        return response()->json(['message' => 'Client created', 'data' => $client], 201);
    }

    public function show(Client $client)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        return response()->json($client);
    }

    public function update(Request $request, Client $client)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);

        $client->update($validated);

        return response()->json(['message' => 'Client updated', 'data' => $client]);
    }

    public function destroy(Client $client)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $client->delete();

        return response()->json(['message' => 'Client deleted']);
    }
}
