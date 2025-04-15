@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Add Menu Item</h2>

        <form action="{{ route('menu-items.store') }}" method="POST">
            @csrf
            @include('menu_items.form')
            <button class="btn btn-success">Create</button>
            <a href="{{ route('menu-items.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
