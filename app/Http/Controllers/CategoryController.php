<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::defaultOrder()->get()->toTree();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::defaultOrder()->get()->toTree();

        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateCategory($request);

        $category = new Category($data);

        if ($data['parent_id']) {
            $category->parent_id = $data['parent_id'];
            $category->save();
        } else {
            $category->saveAsRoot();
        }

        return redirect()->route('admin.categories.index')->with('status', "Category \"{$category->name}\" created.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categories = Category::defaultOrder()->get()->toTree();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $this->validateCategory($request, $category);

        $category->fill($data);

        if ($data['parent_id']) {
            $category->parent_id = $data['parent_id'];
        } else {
            $category->parent_id = null;
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('status', "Category \"{$category->name}\" updated.");
    }

    /**
     * Move a category to a new parent and/or position, as dropped in the tree UI.
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|exists:categories,slug',
            'parent' => 'nullable|string|exists:categories,slug',
            'before' => 'nullable|string|exists:categories,slug',
        ]);

        $category = Category::where('slug', $data['category'])->firstOrFail();
        $parent = $data['parent'] ? Category::where('slug', $data['parent'])->firstOrFail() : null;
        $before = $data['before'] ? Category::where('slug', $data['before'])->firstOrFail() : null;

        abort_if(
            $parent && ($parent->id === $category->id || $category->descendants()->pluck('id')->contains($parent->id)),
            422,
            'A category cannot be moved under itself or one of its own descendants.'
        );

        abort_if($before && $before->id === $category->id, 422, 'Invalid drop target.');

        if ($before) {
            $category->insertBeforeNode($before);
        } elseif ($parent) {
            $category->appendToNode($parent);
        } else {
            $category->makeRoot();
        }

        $category->save();

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $name = $category->name;

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', "Category \"{$name}\" deleted.");
    }

    /**
     * Validate a category create/update request.
     */
    private function validateCategory(Request $request, ?Category $category = null): array
    {
        $parentRules = ['nullable', Rule::exists('categories', 'slug')];

        if ($category) {
            $parentRules[] = Rule::notIn([$category->slug]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_slug' => $parentRules,
        ]);

        $parent = $validated['parent_slug']
            ? Category::where('slug', $validated['parent_slug'])->firstOrFail()
            : null;

        if ($category && $parent) {
            abort_if(
                $category->descendants()->pluck('id')->contains($parent->id),
                422,
                'A category cannot be moved under one of its own descendants.'
            );
        }

        return [
            'name' => $validated['name'],
            'parent_id' => $parent?->id,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
