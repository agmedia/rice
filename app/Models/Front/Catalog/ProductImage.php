<?php

namespace App\Models\Front\Catalog;

use App\Models\Back\Catalog\Product\ProductImageTranslation;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{

    /**
     * @var string
     */
    protected $table = 'product_images';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * @param $value
     *
     * @return array|string|string[]
     */
    public function getImageAttribute($value)
    {
        return str_replace('.jpg', '.webp', $value);
    }


    /**
     * @param $value
     *
     * @return array|string|string[]
     */
    public function getThumbAttribute($value)
    {
        return str_replace('.webp', '-thumb.webp', $this->image);
    }


    public function translation($lang = null, bool $all = false)
    {
        if ($lang) {
            return $this->hasOne(ProductImageTranslation::class, 'product_image_id')->where('lang', $lang)->first();
        }

        if ($all) {
            return $this->hasMany(ProductImageTranslation::class, 'product_image_id');
        }

        return $this->hasOne(ProductImageTranslation::class, 'product_image_id')->where('lang', session('locale'));
    }

}
