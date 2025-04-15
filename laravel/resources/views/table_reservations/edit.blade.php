@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Reservation</h2>

        <form action="{{ route('table-reservations.update', $tableReservation) }}" method="POST">
            @csrf @method('PUT')
            @include('table_reservations.form')
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('table-reservations.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
