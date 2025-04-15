@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create Reservation</h2>

        <form action="{{ route('table-reservations.store') }}" method="POST">
            @csrf
            @include('table_reservations.form')
            <button class="btn btn-success">Create</button>
            <a href="{{ route('table-reservations.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
