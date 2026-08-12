@extends('layouts.app')

@section('meta_title', 'New Category')

@section('content')
<div class="container-lg">
    <div class="card">
        <div class="card-header h4">New Category</div>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                @include('admin.categories.partials.form')
            </form>
        </div>
    </div>
</div>
@endsection
