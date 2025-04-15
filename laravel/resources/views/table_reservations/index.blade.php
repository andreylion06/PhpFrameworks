@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Table Reservations</h2>
        <a href="{{ route('table-reservations.create') }}" class="btn btn-primary mb-3">Add Reservation</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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
    </div>
@endsection
