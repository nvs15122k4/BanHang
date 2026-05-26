<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
        'description',
        'is_new',
    ];

    protected $casts = [
        'is_new' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'loai', 'slug');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'danh-muc';
        $slug = $baseSlug;
        $suffix = 2;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    public static function treeList(): Collection
    {
        $categories = static::orderBy('name')->get();
        $childrenByParent = $categories->groupBy(fn (Category $category) => $category->parent_id ?: 0);
        $flattened = collect();
        $visited = [];

        $appendChildren = function (int $parentId, int $depth, string $parentPath) use (&$appendChildren, &$visited, $childrenByParent, $flattened): void {
            foreach ($childrenByParent->get($parentId, collect()) as $category) {
                if (isset($visited[$category->id])) {
                    continue;
                }

                $visited[$category->id] = true;
                $category->depth = $depth;
                $category->path = $parentPath === '' ? $category->name : $parentPath.' > '.$category->name;
                $category->has_children = $childrenByParent->has($category->id);
                $flattened->push($category);
                $appendChildren($category->id, $depth + 1, $category->path);
            }
        };

        $appendChildren(0, 0, '');

        foreach ($categories as $category) {
            if (! isset($visited[$category->id])) {
                $category->depth = 0;
                $category->path = $category->name;
                $category->has_children = $childrenByParent->has($category->id);
                $flattened->push($category);
                $appendChildren($category->id, 1, $category->path);
            }
        }

        return $flattened;
    }
}
