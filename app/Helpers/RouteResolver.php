<?php

namespace App\Helpers;

use App\Models\Front\Blog;
use App\Models\Front\Catalog\Category;
use App\Models\Front\Recepti;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class RouteResolver
{
    /**
     * Constant representing the blog group type
     */
    private const GROUP_BLOG = 'blog';

    /**
     * Constant representing the recipe group type
     */
    private const GROUP_RECEPTI = 'recepti';

    /**
     * Maps group types to their corresponding route names
     * This mapping centralizes the route configuration and makes it easier to maintain
     */
    private const ROUTE_MAPPING = [
        self::GROUP_BLOG => 'catalog.route.blog',
        self::GROUP_RECEPTI => 'catalog.route.recepti'
    ];

    /**
     * @var string
     */
    private string $group;

    /**
     * @var ?Model
     */
    private ?Model $model;

    /**
     * @var array
     */
    private array $breadcrumbs = [];


    /**
     * @param string      $group
     * @param string|null $model
     */
    public function __construct(string $group, string $model = null)
    {
        $this->group = $group;

        if ($model) {
            $this->setModel($model);
        }
    }


    /**
     * @param string $category
     * @param int    $parent_id
     *
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getCategory(string $category, int $parent_id = 0)
    {
        return Category::query()->whereHas('translation', function ($query) use ($category) {
            $query->where('slug', $category);
        })->where('parent_id', $parent_id)->first();
    }


    /**
     * @param string $slug
     *
     * @return string|Blog
     */
    public function getModel(string $slug): string|Blog|Recepti
    {
        return $this->setModel($slug)->model;
    }


    /**
     * @param Builder  $model_query
     * @param Category $category
     *
     * @return Builder
     */
    public function getModelsByCategory(Builder $model_query, Category $category)
    {
        return $model_query->whereHas('categories', function ($query) use ($category) {
            $query->where('category_id', $category->id);
        });
    }

    /**
     * Creates a standardized breadcrumb item
     *
     * @param string $title The display title for the breadcrumb
     * @param array $params Route parameters including category, subcategory, and group-specific parameters
     * @param bool $isActive Whether this breadcrumb represents the current page
     * @return array The formatted breadcrumb item
     * @throws \InvalidArgumentException If an invalid group type is specified
     */
    private function createBreadcrumbItem(string $title, array $params, bool $isActive): array
    {
        $routeName = self::ROUTE_MAPPING[$this->group]
            ?? throw new \InvalidArgumentException("Invalid group: {$this->group}");

        return [
            'title'  => $title,
            'url'    => route($routeName, $params),
            'active' => $isActive
        ];
    }

    /**
     * Builds the complete breadcrumb trail based on the current category, subcategory, and model
     *
     * @param Category|null $category The main category
     * @param Category|null $subcategory The subcategory, if any
     * @param mixed $model The content model (blog post or recipe)
     * @return array The complete breadcrumb trail
     */
    public function attachBreadcrumbs(Category $category = null, Category $subcategory = null, ?Model $model = null): array
    {
        // Add main category breadcrumb if present
        if ($category) {
            $this->breadcrumbs[] = $this->createBreadcrumbItem(
                $category->title,
                ['cat' => $category->slug, 'subcat' => null, $this->group => null],
                (bool)$subcategory
            );
        }

        if ($subcategory && $category) {
            $this->breadcrumbs[] = $this->createBreadcrumbItem(
                $subcategory->title,
                ['cat' => $category->slug, 'subcat' => $subcategory->slug, $this->group => null],
                (bool)$model
            );
        }

        if ($model) {
            if ($model->category()) {
                $this->breadcrumbs[] = $this->createBreadcrumbItem(
                    $model->category()->title,
                    ['cat' => $model->category()->slug, 'subcat' => null, $this->group => null],
                    true
                );
            }

            if ($model->subcategory()) {
                $this->breadcrumbs[] = $this->createBreadcrumbItem(
                    $model->subcategory()->title,
                    ['cat' => $model->category()->slug, 'subcat' => $model->subcategory()->slug, $this->group => null],
                    true
                );
            }

            $this->breadcrumbs[] = $this->createBreadcrumbItem(
                $model->title,
                ['cat' => $model->slug],
                false
            );
        }

        return $this->breadcrumbs;
    }


    /**
     * @param string $model
     *
     * @return RouteResolver
     */
    private function setModel(string $model): RouteResolver
    {
        if ($this->group === 'blog') {
            $this->model = Blog::query()->whereHas('translation', function ($query) use ($model) {
                $query->where('slug', $model);
            })->where('status', 1)->first();
        }

        if ($this->group === 'recepti') {
            $this->model = Recepti::query()->whereHas('translation', function ($query) use ($model) {
                $query->where('slug', $model);
            })->where('status', 1)->first();
        }

        return $this;
    }

}