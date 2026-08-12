@foreach ($categories as $category)
    <li class="mb-1" data-slug="{{ $category->slug }}">
        <div class="d-flex align-items-center justify-content-between border rounded px-2 py-1 bg-body">
            <span title="Title: {{ $category->name }}" class="{{ $category->is_active ? '' : 'text-body-secondary' }}">
                <i class="bi bi-folder-fill {{ $category->is_active ? 'text-warning' : 'text-body-secondary' }} me-1"></i>
                {{ $category->name }}
                @unless ($category->is_active)
                    <span class="badge text-bg-secondary ms-1">Inactive</span>
                @endunless
            </span>
            <span>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="{{ route('admin.categories.create', ['parent' => $category->slug]) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-plus"></i>
                    </a>
                    <button class="btn btn-outline-danger" onclick="return confirm('Delete &quot;{{ $category->name }}&quot; and all its subcategories?');">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <i class="bi bi-grip-vertical drag-handle text-body-secondary me-1" style="cursor: grab;"></i>
            </span>
        </div>
        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
        </form>

        <ul class="list-unstyled ms-4 mt-1 mb-0 category-children" data-parent-slug="{{ $category->slug }}">
            @include('admin.categories.partials.tree', ['categories' => $category->children])
        </ul>
    </li>
@endforeach
