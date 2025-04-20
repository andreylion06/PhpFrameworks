<?php

namespace App\Http\Controllers;

use App\Models\TableReservation;
use Illuminate\Http\Request;

class TableReservationController extends BaseController
{
    public function index(Request $request)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $query = TableReservation::with('client');

        if ($request->has('client_id') && $request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->has('table_number') && $request->filled('table_number')) {
            $query->where('table_number', $request->input('table_number'));
        }

        if ($request->has('reservation_date') && $request->filled('reservation_date')) {
            $query->whereDate('reservation_date', $request->input('reservation_date'));
        }

        $itemsPerPage = (int) $request->input('itemsPerPage', 10);
        $reservations = $query->paginate($itemsPerPage)->appends($request->query());

        return response()->json([
            'data' => $reservations->items(),
            'pagination' => [
                'currentPage' => $reservations->currentPage(),
                'totalItems' => $reservations->total(),
                'itemsPerPage' => $reservations->perPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'table_number' => 'required|integer|min:1',
            'reservation_date' => 'required|date',
        ]);

        $reservation = TableReservation::create($validated);

        return response()->json(['message' => 'Reservation created', 'data' => $reservation], 201);
    }

    public function show(TableReservation $tableReservation)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        return response()->json($tableReservation->load('client'));
    }

    public function update(Request $request, TableReservation $tableReservation)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'table_number' => 'required|integer|min:1',
            'reservation_date' => 'required|date',
        ]);

        $tableReservation->update($validated);

        return response()->json(['message' => 'Reservation updated', 'data' => $tableReservation]);
    }

    public function destroy(TableReservation $tableReservation)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $tableReservation->delete();

        return response()->json(['message' => 'Reservation deleted']);
    }
}
