<?php

namespace App\Helpers;

use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Orders\OrderProduct;
use App\Models\Front\Catalog\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductHelper
{

    /**
     * @param Product       $product
     * @param Category|null $category
     * @param Category|null $subcategory
     *
     * @return string
     */
    public static function categoryString(Product $product, Category $category = null, Category $subcategory = null): string
    {
        $data        = static::resolveCategories($product, $category, $subcategory);
        $category    = $data['category'];
        $subcategory = $data['subcategory'];
        $catstring   = '';

        if ($category) {
            $catstring = '<a href="' . route('catalog.route', ['group' => Str::slug($category->group), 'cat' => $category->slug]) . '" class="product-meta maincat d-block fs-xs pb-1">' . $category->title . '</a> ';
        }

        if ($subcategory) {
            $substring = '<a href="' . route('catalog.route',
                    ['group' => Str::slug($category->group), 'cat' => $category->slug, 'subcat' => $subcategory->slug]) . '" class="product-meta subcat d-block fs-xs pb-1">' . $subcategory->title . '</a>';

            return $catstring . $substring;
        }

        return $catstring;
    }


    /**
     * @param Product       $product
     * @param Category|null $category
     * @param Category|null $subcategory
     *
     * @return string
     */
    public static function url(Product $product, Category $category = null, Category $subcategory = null, string $locale = null): string
    {
        $_locale     = $locale ?: current_locale();
        $data        = static::resolveCategories($product, $category, $subcategory);
        $category    = $data['category'];
        $subcategory = $data['subcategory'];

        if ($subcategory) {
            return $_locale . '/' . Str::slug($category->group) . '/' . $category->translation($_locale)->slug . '/' . $subcategory->translation($_locale)->slug . '/' . $product->translation($_locale)->slug;
        }

        if ($category) {
            return $_locale . '/' . Str::slug($category->group) . '/' . $category->translation($_locale)->slug . '/' . $product->translation($_locale)->slug;
        }

        return $_locale . '/';
    }


    /**
     * @param Product       $product
     * @param Category|null $category
     * @param Category|null $subcategory
     *
     * @return array
     */
    public static function resolveCategories(Product $product, Category $category = null, Category $subcategory = null): array
    {
        if ( ! $category) {
            $category = $product->category();
        }

        if ( ! $subcategory) {
            $psub = $product->subcategory();

            if ($psub and $category) {
                foreach ($category->subcategories()->get() as $sub) {
                    if ($sub->id == $psub->id) {
                        $subcategory = $psub;
                    }
                }
            }
        }

        return [
            'category'    => $category,
            'subcategory' => $subcategory
        ];
    }


    /**
     * @param Builder $query
     * @param array   $request
     *
     * @return Builder
     */
    public static function queryCategories(Builder $query, array $request): Builder
    {
        $query->whereHas('categories', function ($query) use ($request) {
            if ($request['group'] && ! $request['cat'] && ! $request['subcat']) {
                $query->where('group', $request['group']);
            }

            if ($request['cat'] && ! $request['subcat']) {
                $query->where('category_id', $request['cat']);
            }

            if ($request['subcat']) {
                $query->where('category_id', $request['subcat']);
            }
        });

        return $query;
    }


    /**
     * @param string $path
     *
     * @return string
     */
    public static function getCleanImageTitle(string $path): string
    {
        $from   = strrpos($path, '/') + 1;
        $length = strrpos($path, '-') - $from;

        return substr($path, $from, $length);
    }


    /**
     * @param string $path
     *
     * @return string
     */
    public static function getFullImageTitle(string $path): string
    {
        $from   = strrpos($path, '/') + 1;
        $length = strrpos($path, '.') - $from;

        return substr($path, $from, $length);
    }


    /**
     * @param string $title
     *
     * @return string
     */
    public static function setFullImageTitle(string $title): string
    {
        return $title . '-' . Str::random(4);
    }


    /**
     * @param int|string $order_id
     *
     * @return bool
     */
    public static function makeAvailable($order_id): bool
    {
        $ops = OrderProduct::query()->where('order_id', $order_id)->get();

        foreach ($ops as $op) {
            Product::query()->where('id', $op->product_id)->increment('quantity', $op->quantity);
        }

        return true;
    }


    /**
     * @param $description
     *
     * @return string
     */
    public static function cleanHTML($description = null): string
    {
        $clean = preg_replace('/ style=("|\')(.*?)("|\')/', '', $description ?: '');

        return preg_replace('/ face=("|\')(.*?)("|\')/', '', $clean);
    }


    /**
     * @param int $product_id
     *
     * @return int
     */
    public static function checkComboProductSession(int $product_id = 0): int
    {
        if ($product_id) {
            if (session()->has('combo.' . $product_id)) {
                //Log::info(session('combo.' . $product_id));
                return 1;
            }
        }

        return 0;
    }
}
