<?php

namespace App\Helpers\ModelResolvers;

use App\Models\Front\Recepti;
use Illuminate\Database\Eloquent\Model;

/**
 * Resolver for Recepti models
 */
class ReceptiResolver implements ModelResolverInterface
{
    /**
     * Resolve and retrieve a Recepti model based on its slug
     *
     * @param string $slug The URL slug of the recipe
     * @return Model|null The found recipe or null if not found
     */
    public function resolveModel(string $slug): ?Model
    {
        return Recepti::query()
                      ->whereHas('translation', fn($query) => $query->where('slug', $slug))
                      ->where('status', 1)
                      ->first();
    }
}