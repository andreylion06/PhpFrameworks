@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create Order Item</h2>

        <form action="{{ route('order-items.store') }}" method="POST">
            @csrf
            @include('order_items.form')
            <button class="btn btn-success">Create</button>
            <a href="{{ route('order-items.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
