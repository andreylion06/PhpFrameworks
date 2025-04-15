@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Menu Item</h2>

        <form action="{{ route('menu-items.update', $menuItem) }}" method="POST">
            @csrf @method('PUT')
            @include('menu_items.form')
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('menu-items.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
