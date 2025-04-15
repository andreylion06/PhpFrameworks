@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Reservation Details</h2>
        <ul class="list-group">
            <li class="list-group-item"><strong>Client:</strong> {{ $tableReservation->client->name }}</li>
            <li class="list-group-item"><strong>Table Number:</strong> {{ $tableReservation->table_number }}</li>
            <li class="list-group-item"><strong>Reservation Date:</strong> {{ $tableReservation->reservation_date }}</li>
        </ul>
        <a href="{{ route('table-reservations.index') }}" class="btn btn-secondary mt-3">Back</a>
    </div>
@endsection
