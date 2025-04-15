@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Order Details</h2>
        <ul class="list-group">
            <li class="list-group-item"><strong>ID:</strong> {{ $order->id }}</li>
            <li class="list-group-item"><strong>Client:</strong> {{ $order->client->name }}</li>
            <li class="list-group-item"><strong>Created At:</strong> {{ $order->created_at }}</li>
        </ul>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">Back to list</a>
    </div>
@endsection
