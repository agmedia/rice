<?php

namespace App\Helpers;

use App\Models\Front\Blog;
use App\Models\Front\Catalog\Category;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class RouteResolver
{

    /**
     * @var string
     */
    private $group = '';

    /**
     * @var string
     */
    private $category = '';

    /**
     * @var string
     */
    private $subcategory = '';

    /**
     * @var string
     */
    private $model = '';


    private $breadcrumbs = [];


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
    public function getModel(string $slug): string|Blog
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


    public function attachBreadcrumbs(Category $category = null, Category $subcategory = null, $model = null)
    {
        if ($category) {
            $this->breadcrumbs[] = [
                'title' => $category->title,
                'url' => route('catalog.route.blog', [
                    'cat'    => $category->slug,
                    'subcat' => null,
                    'blog'   => null
                ]),
                'active' => $subcategory ? true : false
            ];
        }

        if ($subcategory) {
            $this->breadcrumbs[] = [
                'title' => $subcategory->title,
                'url' => route('catalog.route.blog', [
                    'cat'    => $category->slug,
                    'subcat' => $subcategory->slug,
                    'blog'   => null
                ]),
                'active' => $model ? true : false
            ];
        }

        if ($model) {
            $this->breadcrumbs[] = [
                'title' => $subcategory->title,
                'url' => route('catalog.route.blog', [
                    'cat'    => $category->slug,
                    'subcat' => $subcategory->slug,
                    'blog'   => $model->slug
                ]),
                'active' => false
            ];
        }
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

        return $this;
    }
}
