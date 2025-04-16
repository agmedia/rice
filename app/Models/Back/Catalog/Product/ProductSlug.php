<?php

namespace App\Models\Back\Catalog\Product;

use Illuminate\Database\Eloquent\Model;

class ProductSlug extends Model
{

    /**
     * @var string
     */
    protected $table = 'product_slug';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * @param string $slug
     * @param string $lang
     * @param int    $id
     *
     * @return bool
     */
    public static function create(string $slug, string $lang, int $id): bool
    {
        $exist = self::query()->where('slug', $slug)->where('lang', $lang)->first();

        if ( ! $exist) {
            return self::query()->insert([
                'product_id' => $id,
                'lang'       => $lang,
                'slug'       => $slug,
            ]);
        }

        return false;
    }

}
