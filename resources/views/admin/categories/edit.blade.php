@extends('layouts.app')

@section('meta_title', 'Edit Category')

@section('content')
<div class="container-lg">
    <div class="card">
        <div class="card-header h4">Edit Category</div>
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.categories.partials.form')
            </form>
        </div>
    </div>
</div>
@endsection
