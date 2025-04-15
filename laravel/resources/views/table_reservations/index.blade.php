@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Table Reservations</h2>
        <a href="{{ route('table-reservations.create') }}" class="btn btn-primary mb-3">Add Reservation</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="client_id" class="form-select">
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <input type="number" name="table_number" class="form-control" placeholder="Table #" value="{{ request('table_number') }}">
            </div>

            <div class="col-md-3">
                <input type="date" name="reservation_date" class="form-control" value="{{ request('reservation_date') }}">
            </div>

            <div class="col-md-2">
                <input type="number" name="itemsPerPage" class="form-control" placeholder="#" value="{{ request('itemsPerPage', 10) }}">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </form>

        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Client</th><th>Table</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach ($reservations as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->client->name }}</td>
                    <td>{{ $r->table_number }}</td>
                    <td>{{ $r->reservation_date }}</td>
                    <td>
                        <a href="{{ route('table-reservations.show', $r) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('table-reservations.edit', $r) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('table-reservations.destroy', $r) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this reservation?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $reservations->links('pagination::bootstrap-5') }}
    </div>
@endsection
