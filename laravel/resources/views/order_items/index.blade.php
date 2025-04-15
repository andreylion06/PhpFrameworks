@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Order Items</h2>
        <a href="{{ route('order-items.create') }}" class="btn btn-primary mb-3">Add New Order Item</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Order</th><th>Menu Item</th><th>Quantity</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach ($orderItems as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>Order #{{ $item->order_id }}</td>
                    <td>{{ $item->menuItem->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        <a href="{{ route('order-items.show', $item) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('order-items.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('order-items.destroy', $item) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
