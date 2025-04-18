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
            '@context'=> 'https://schema.org',
            '@type'=> 'LocalBusiness',
            '@id'=> config('app.url') . '#store',
            'name'=> config('app.name'),
            'image'=> asset('img/logo-kakis.png'),
            'logo'=> asset('img/logo-kakis.png'),
            'url'=> config('app.url'),
            'address'=> [
                '@type'=> 'PostalAddress',
                'streetAddress'=> 'Petrinjska 9',
                'addressLocality'=> 'Zagreb',
                'postalCode'=> '10000',
                'addressCountry'=> 'HR'
            ],
            'geo'=> [
                '@type'=> 'GeoCoordinates',
                'latitude'=> 45.808,
                'longitude'=> 15.978
            ],
            'telephone'=> '+385915207047',
            'openingHours'=> [
                'Mo-Fr 11:00-19:00',
                'Sa 10:00-18:00'
            ],
            'priceRange'=> '€€',
            'sameAs'=> [
                'https://www.facebook.com/ricekakis',
                'https://www.instagram.com/ricekakis',
                'https://www.tiktok.com/@ricekakis'
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
            $price = ($prod->special()) ? $prod->special() : number_format($prod->price, 2, '.', '');

            $response = [
                '@context' => 'https://schema.org/',
                '@type' => 'Product',
                'sku' => $prod->sku,
                'description' => $prod->translation->meta_description,
                'name' => $prod->name,
                'url' => url($prod->url),
                'itemCondition' => 'https://schema.org/NewCondition',
                'image' => [
                    '@type' => 'ImageObject',
                    'url' => asset($prod->image),
                    'name' => $prod->alt,
                    'width' => 500,
                    'height' => 500,
                ],
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $prod->brand->title,
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'priceCurrency' => 'EUR',
                    'price' => (string) $price,
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
