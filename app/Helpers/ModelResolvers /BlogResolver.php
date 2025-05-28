<?php

namespace App\Helpers\ModelResolvers;

use App\Models\Front\Blog;
use Illuminate\Database\Eloquent\Model;

/**
 * Resolver for Blog models
 */
class BlogResolver implements ModelResolverInterface
{
    /**
     * Resolve and retrieve a Blog model based on its slug
     *
     * @param string $slug The URL slug of the blog post
     * @return Model|null The found blog post or null if not found
     */
    public function resolveModel(string $slug): ?Model
    {
        return Blog::query()
                   ->whereHas('translation', fn($query) => $query->where('slug', $slug))
                   ->where('status', 1)
                   ->first();
    }
}