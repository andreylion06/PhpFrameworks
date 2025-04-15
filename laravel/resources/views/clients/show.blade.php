@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Client Details</h2>

        <ul class="list-group">
            <li class="list-group-item"><strong>ID:</strong> {{ $client->id }}</li>
            <li class="list-group-item"><strong>Name:</strong> {{ $client->name }}</li>
            <li class="list-group-item"><strong>Email:</strong> {{ $client->email }}</li>
            <li class="list-group-item"><strong>Phone:</strong> {{ $client->phone }}</li>
        </ul>

        <a href="{{ route('clients.index') }}" class="btn btn-secondary mt-3">Back to list</a>
    </div>
@endsection
