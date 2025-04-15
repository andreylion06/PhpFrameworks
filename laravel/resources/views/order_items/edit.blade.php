@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Order Item</h2>

        <form action="{{ route('order-items.update', $orderItem) }}" method="POST">
            @csrf @method('PUT')
            @include('order_items.form')
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('order-items.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
