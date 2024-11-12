<?php

namespace App\Helpers;


use App\Models\Front\Catalog\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class Metatags
{

    public static function noFollow()
    {
        return [
            'name' => 'robots',
            'content' => 'noindex,nofollow'
        ];
    }


    /**
     * @return array
     */
    public static function indexSchema(): array
    {
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'logo' => asset('img/logo-kakis.png'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+385 99 3334448',
                'contactType' => 'Customer Service'
            ]
        ];
    }


    /**
     * @param Product|null    $prod
     * @param Collection|null $reviews
     *
     * @return array
     */
    public static function productSchema(Product $prod = null, Collection $reviews = null): array
    {
        $response = [];

        if ($prod) {
            $response = [
                '@context' => 'https://schema.org/',
                '@type' => 'Product',
                'description' => $prod->translation->meta_description,
                'name' => $prod->name,
                'image' => asset($prod->image),
                //'url' => url($prod->url),
                'offers' => [
                    '@type' => 'Offer',
                    'priceCurrency' => 'EUR',
                    'price' => ($prod->special()) ? $prod->special() : number_format($prod->price, 2, '.', ''),
                    'sku' => $prod->sku,
                    'availability' => ($prod->quantity) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
                ],
            ];

            if ($reviews->count()) {
                $response['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => floor($reviews->avg('stars')),
                    'reviewCount' => $reviews->count(),
                ];

                foreach ($reviews as $review) {
                    $res_review = [
                        '@type' => 'Review',
                        'author' => $review->fname,
                        'datePublished' => Carbon::make($review->created_at)->locale('hr')->format('Y-m-d'),
                        'reviewBody' => strip_tags($review->message),
                        'name' => $prod->name,
                        'reviewRating' => [
                            '@type' => 'Rating',
                            'bestRating' => '5',
                            'ratingValue' => floor($review->stars),
                            'worstRating' => '1'
                        ]
                    ];
                }

                $response['review'] = $res_review;
            }
        }

        return $response;
    }
}
