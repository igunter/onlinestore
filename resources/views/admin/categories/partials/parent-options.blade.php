@unless ($excludedSlugs->contains($option->slug))
    <option value="{{ $option->slug }}" @selected($selectedParentSlug === $option->slug)>
        {{ str_repeat('— ', $depth) }}{{ $option->name }}
    </option>
@endunless

@foreach ($option->children as $child)
    @include('admin.categories.partials.parent-options', ['option' => $child, 'depth' => $depth + 1, 'selectedParentSlug' => $selectedParentSlug, 'excludedSlugs' => $excludedSlugs])
@endforeach
