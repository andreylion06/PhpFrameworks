@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Menu Item Details</h2>
        <ul class="list-group">
            <li class="list-group-item"><strong>Name:</strong> {{ $menuItem->name }}</li>
            <li class="list-group-item"><strong>Description:</strong> {{ $menuItem->description }}</li>
            <li class="list-group-item"><strong>Price:</strong> {{ number_format($menuItem->price, 2) }} $</li>
        </ul>
        <a href="{{ route('menu-items.index') }}" class="btn btn-secondary mt-3">Back to list</a>
    </div>
@endsection
