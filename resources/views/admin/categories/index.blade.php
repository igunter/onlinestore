@extends('layouts.app')

@section('meta_title', 'Categories')

@push('styles')
    <style>
        .category-children:empty {
            min-height: 1.5rem;
        }

        .category-children:empty::after {
            content: '\B7\B7\B7';
            display: block;
            padding: 0.125rem 0.5rem;
            border: 1px dashed var(--bs-border-color);
            border-radius: 0.25rem;
            color: var(--bs-secondary-color);
            font-size: 0.75rem;
            line-height: 1.2;
        }

        .category-row {
            transition: background-color 0.1s ease-in-out;
        }

        .category-row:hover {
            background-color: var(--bs-tertiary-bg);
        }

        /* Only show the row itself in the floating drag clone, not its nested children */
        .sortable-fallback .category-children {
            display: none !important;
        }

        .category-ghost {
            opacity: 0.4;
        }

        .category-ghost .category-row {
            border-style: dashed !important;
        }

        .category-chosen > .category-row {
            box-shadow: 0 0 0 2px var(--bs-primary);
        }
    </style>
@endpush

@section('content')
<div class="container-lg">
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-header h4">
            Categories
            <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary float-end">
                <i class="bi bi-plus-lg"></i> New Category
            </a>
        </div>
        <div class="card-body">
            <p class="text-body-secondary small">Drag the <i class="bi bi-grip-vertical"></i> handle to reorder categories.</p>

            @if ($categories->isEmpty())
                <p class="text-body-secondary mb-0">No categories yet.</p>
            @else
                <ul class="list-unstyled mb-0 category-children" data-parent-slug="">
                    @include('admin.categories.partials.tree', ['categories' => $categories])
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
        document.querySelectorAll('.category-children').forEach(function (list) {
            new Sortable(list, {
                group: 'categories',
                animation: 150,
                handle: '.drag-handle',
                fallbackOnBody: true,
                swapThreshold: 0.65,
                ghostClass: 'category-ghost',
                chosenClass: 'category-chosen',
                onEnd: function (evt) {
                    var item = evt.item;
                    var newParentList = evt.to;
                    var nextSibling = item.nextElementSibling;

                    var payload = {
                        category: item.dataset.slug,
                        parent: newParentList.dataset.parentSlug || null,
                        before: nextSibling ? nextSibling.dataset.slug : null,
                    };

                    fetch('{{ route('admin.categories.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error('Reorder failed');
                        }
                    }).catch(function () {
                        alert('Could not save the new category order. Reloading.');
                        window.location.reload();
                    });
                },
            });
        });
    </script>
@endpush
