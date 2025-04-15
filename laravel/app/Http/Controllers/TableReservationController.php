<?php

namespace App\Http\Controllers;

use App\Models\TableReservation;
use App\Models\Client;
use Illuminate\Http\Request;

class TableReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = TableReservation::with('client');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('table_number')) {
            $query->where('table_number', $request->table_number);
        }

        if ($request->filled('reservation_date')) {
            $query->whereDate('reservation_date', $request->reservation_date);
        }

        $itemsPerPage = $request->input('itemsPerPage', 10);
        $reservations = $query->paginate($itemsPerPage)->appends($request->query());

        $clients = Client::all();

        return view('table_reservations.index', compact('reservations', 'clients'));
    }

    public function create()
    {
        $clients = Client::all();
        return view('table_reservations.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'table_number' => 'required|integer|min:1',
            'reservation_date' => 'required|date',
        ]);

        TableReservation::create($request->all());

        return redirect()->route('table-reservations.index')->with('success', 'Reservation created successfully.');
    }

    public function show(TableReservation $tableReservation)
    {
        return view('table_reservations.show', compact('tableReservation'));
    }

    public function edit(TableReservation $tableReservation)
    {
        $clients = Client::all();
        return view('table_reservations.edit', compact('tableReservation', 'clients'));
    }

    public function update(Request $request, TableReservation $tableReservation)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'table_number' => 'required|integer|min:1',
            'reservation_date' => 'required|date',
        ]);

        $tableReservation->update($request->all());

        return redirect()->route('table-reservations.index')->with('success', 'Reservation updated successfully.');
    }

    public function destroy(TableReservation $tableReservation)
    {
        $tableReservation->delete();

        return redirect()->route('table-reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
