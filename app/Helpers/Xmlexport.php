<?php

namespace App\Helpers;

use App\Models\Back\Catalog\Category;
use App\Models\Front\Catalog\Product;
use Illuminate\Support\Facades\Log;
use App\Models\Front\Catalog\Brand;

/**
 *
 */
class Xmlexport
{

    /**
     * @var array
     */
    private $response = [];


    /**
     * @return array
     */
    public function getItems(): array
    {

        $products = Product::query()
                           ->with('images', 'translation')
                           ->take(1000)
                           ->get();

        foreach ($products as $product) {
            $this->response[] = [
                'id'          => $product->id,
                'name'        => $product->translation->name,
                'description' => strip_tags($product->translation->description),
                'brand'       => $product->brand ? $product->brand->title : '',
                'brand_id'    => $product->brand_id,
                'price'       => number_format($product->price, 2),
                'sku'         => $product->sku,
                'quantity'    => $product->quantity,
                'status'      => $product->status,
                'slug'        => 'https://www.ricekakis.com/' . $product->translation->url,
                'image'       => asset($product->image),
            ];
        }

        return $this->response;
    }


    /**
     * @param Product $product
     *
     * @return string
     */
    private function getDescription(Product $product): string
    {
        $str = '';

        if ($product->description != '') {
            $str .= preg_replace('/[[:cntrl:]]/', '', $product->description) . '<br><br>';


        }

        return $str;
    }
}
