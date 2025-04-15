@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Clients</h2>
        <a href="{{ route('clients.create') }}" class="btn btn-primary mb-3">Add New Client</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('clients.index') }}" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Name" value="{{ request('name') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="email" class="form-control" placeholder="Email" value="{{ request('email') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ request('phone') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="itemsPerPage" class="form-control" placeholder="Items per page" value="{{ request('itemsPerPage', 10) }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->phone }}</td>
                    <td>
                        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this client?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $clients->links('pagination::bootstrap-5') }}
    </div>
@endsection
