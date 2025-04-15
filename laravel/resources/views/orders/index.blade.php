@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Orders</h2>
        <a href="{{ route('orders.create') }}" class="btn btn-primary mb-3">Add New Order</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <select name="client_id" class="form-select">
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="created_at" class="form-control" value="{{ request('created_at') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="itemsPerPage" class="form-control" placeholder="Items per page" value="{{ request('itemsPerPage', 10) }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </form>

        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Client</th><th>Created At</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->client->name }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this order?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
@endsection
