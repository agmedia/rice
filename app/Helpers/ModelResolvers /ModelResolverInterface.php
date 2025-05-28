<?php

namespace App\Helpers\ModelResolvers;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for model resolvers that handle slug-based model retrieval
 */
interface ModelResolverInterface
{
    /**
     * Resolve and retrieve a model instance based on a slug
     *
     * @param string $slug The URL slug to look up
     * @return Model|null The found model or null if not found
     */
    public function resolveModel(string $slug): ?Model;
}