@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Order Item Details</h2>
        <ul class="list-group">
            <li class="list-group-item"><strong>Order:</strong> Order #{{ $orderItem->order_id }}</li>
            <li class="list-group-item"><strong>Menu Item:</strong> {{ $orderItem->menuItem->name }}</li>
            <li class="list-group-item"><strong>Quantity:</strong> {{ $orderItem->quantity }}</li>
        </ul>
        <a href="{{ route('order-items.index') }}" class="btn btn-secondary mt-3">Back to list</a>
    </div>
@endsection
