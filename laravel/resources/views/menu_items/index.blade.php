@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Menu Items</h2>
        <a href="{{ route('menu-items.create') }}" class="btn btn-primary mb-3">Add New Item</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Name" value="{{ request('name') }}">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="price_min" class="form-control" placeholder="Min Price" value="{{ request('price_min') }}">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="price_max" class="form-control" placeholder="Max Price" value="{{ request('price_max') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="itemsPerPage" class="form-control" placeholder="Items per page" value="{{ request('itemsPerPage', 10) }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </form>

        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach ($menuItems as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ number_format($item->price, 2) }} $</td>
                    <td>
                        <a href="{{ route('menu-items.show', $item) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('menu-items.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('menu-items.destroy', $item) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $menuItems->links('pagination::bootstrap-5') }}
    </div>
@endsection
