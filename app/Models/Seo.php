<?php

namespace App\Models;

use App\Helpers\Metatags;
use App\Models\Front\Catalog\Author;
use App\Models\Front\Catalog\Brand;
use App\Models\Front\Catalog\Category;
use App\Models\Front\Catalog\Product;
use App\Models\Front\Catalog\Publisher;
use Illuminate\Http\Request;

/**
 * Class Sitemap
 * @package App\Models
 */
class Seo
{


    /**
     * @return array
     */
    public static function getProductData(Product $product): array
    {
        return [
            'main_title'       => rtrim($product->name) ,
            'title'       => rtrim($product->translation->meta_title) ,
            'description' => isset($product->translation->meta_description) ? $product->translation->meta_description : ''
        ];
    }


    /**
     * @return array
     */
    public static function getAuthorData(Author $author, Category $cat = null, Category $subcat = null): array
    {
        $title = $author->title . ' Rice Kakis | Asian Store';
        $description =  $author->meta_description ;

        // Check if there is meta title or description and set vars.
        if ($cat) {
            if ($cat->meta_title) { $title = $cat->meta_title; }
            if ($cat->meta_description) { $description = $cat->meta_description; }
        }

        if ($subcat) {
            if ($subcat->meta_title) { $title = $subcat->meta_title; }
            if ($subcat->meta_description) { $description = $subcat->meta_description; }
        }

        return [
            'title'       => $title,
            'description' => $description
        ];
    }


    /**
     * @return array
     */
    public static function getBrandData(Brand $brand, Category $cat = null, Category $subcat = null): array
    {
        $title = $brand->title . ' Rice Kakis | Asian Store';
        $description =  $brand->translation->meta_description ;



        // Check if there is meta title or description and set vars.
        if ($cat) {
            if ($cat->meta_title) { $title = $cat->meta_title; }
            if ($cat->meta_description) { $description = $cat->meta_description; }
        }

        if ($subcat) {
            if ($subcat->meta_title) { $title = $subcat->meta_title; }
            if ($subcat->meta_description) { $description = $subcat->meta_description; }
        }

        return [
            'title'       => $title,
            'description' => $description
        ];
    }


    /**
     * @return array
     */
    public static function getPublisherData(Publisher $publisher, Category $cat = null, Category $subcat = null): array
    {
        $title = $publisher->title . ' knjige - Plava Krava';
        $description = 'Ponuda knjiga nakladnika ' . $publisher->title . ' u Plava Krava Online shopu. Naručite knjige na engleskom jeziku iz naklade ' . $publisher->title . '.';

        // Check if there is meta title or description and set vars.
        if ($cat) {
            if ($cat->meta_title) { $title = $cat->meta_title; }
            //if ($cat->meta_description) { $description = $cat->meta_description; }
        }

        if ($subcat) {
            if ($subcat->meta_title) { $title = $subcat->meta_title; }
            //if ($subcat->meta_description) { $description = $subcat->meta_description; }
        }

        return [
            'title'       => $title,
            'description' => $description
        ];
    }


    public static function getMetaTags(Request $request, $target = 'product')
    {
        $response = [];
        $data = $request->toArray();

        if ($target == 'filter') {
            if (array_key_exists('start', $data) || array_key_exists('end', $data) || array_key_exists('autor', $data) || array_key_exists('nakladnik', $data)) {
                array_push($response, Metatags::noFollow());
            }
        }

        if ($target == 'ap_filter') {
            if (array_key_exists('letter', $data)) {
                array_push($response, Metatags::noFollow());
            }
        }

        return $response;
    }


}
