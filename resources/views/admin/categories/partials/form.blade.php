@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $selectedParentSlug = old('parent_slug', $category->parent?->slug ?? request('parent', ''));
    $excludedSlugs = isset($category) ? $category->descendants()->pluck('slug')->push($category->slug) : collect();
@endphp

<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required autofocus>
</div>

<div class="mb-3">
    <label for="parent_slug" class="form-label">Parent category</label>
    <select name="parent_slug" id="parent_slug" class="form-select">
        <option value="">— None (top level) —</option>
        @foreach ($categories as $option)
            @include('admin.categories.partials.parent-options', ['option' => $option, 'depth' => 0, 'selectedParentSlug' => $selectedParentSlug, 'excludedSlugs' => $excludedSlugs])
        @endforeach
    </select>
</div>

<div class="mb-3 form-check">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" @checked(old('is_active', $category->is_active ?? true))>
    <label for="is_active" class="form-check-label">Active</label>
    <div class="form-text">Inactive categories, and all their subcategories, are hidden from customers.</div>
</div>

<button type="submit" class="btn btn-primary">Save</button>
<a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
