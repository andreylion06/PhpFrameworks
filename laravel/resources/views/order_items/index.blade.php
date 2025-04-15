@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Order Items</h2>
        <a href="{{ route('order-items.create') }}" class="btn btn-primary mb-3">Add New Order Item</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="order_id" class="form-select">
                    <option value="">-- Order --</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>
                            Order #{{ $order->id }} ({{ $order->client->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select name="menu_item_id" class="form-select">
                    <option value="">-- Menu Item --</option>
                    @foreach($menuItems as $item)
                        <option value="{{ $item->id }}" {{ request('menu_item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <input type="number" name="quantity_min" class="form-control" placeholder="Min Qty" value="{{ request('quantity_min') }}">
            </div>

            <div class="col-md-2">
                <input type="number" name="quantity_max" class="form-control" placeholder="Max Qty" value="{{ request('quantity_max') }}">
            </div>

            <div class="col-md-1">
                <input type="number" name="itemsPerPage" class="form-control" placeholder="#" value="{{ request('itemsPerPage', 10) }}">
            </div>

            <div class="col-md-1">
                <button class="btn btn-primary">Filter</button>
            </div>
        </form>

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

        {{ $orderItems->links('pagination::bootstrap-5') }}
    </div>
@endsection
