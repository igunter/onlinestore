<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Categories that are active and have no inactive ancestor,
     * i.e. safe to show to customers.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_active', true)->whereNotExists(function ($sub) {
            $sub->selectRaw('1')
                ->from('categories as ancestors')
                ->whereColumn('ancestors._lft', '<', 'categories._lft')
                ->whereColumn('ancestors._rgt', '>', 'categories._rgt')
                ->where('ancestors.is_active', false);
        });
    }
}
