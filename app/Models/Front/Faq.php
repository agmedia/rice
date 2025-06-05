<?php

namespace App\Models\Front;

use App\Models\Back\Settings\FaqTranslation;
use App\Models\Front\Catalog\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Faq extends Model
{

    /**
     * @var string
     */
    protected $table = 'faq';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var string
     */
    protected $locale = 'en';


    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->locale = current_locale();
    }


    /**
     * @param null  $lang
     * @param false $all
     *
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasOne|object|null
     */
    public function translation($lang = null, bool $all = false)
    {
        if ($lang) {
            return $this->hasOne(FaqTranslation::class, 'faq_id')->where('lang', $lang)->first();
        }

        if ($all) {
            return $this->hasMany(FaqTranslation::class, 'faq_id');
        }

        return $this->hasOne(FaqTranslation::class, 'faq_id')->where('lang', $this->locale);
    }


    /**
     * @return string
     */
    public function getTitleAttribute()
    {
        return $this->translation->title;
    }


    /**
     * @return string
     */
    public function getDescriptionAttribute()
    {
        return $this->translation->description;
    }


    /**
     * Get FAQ items by category and subcategory
     *
     * @param Category|null $category    Main category
     * @param mixed|null    $subcategory Subcategory object
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCategoryList(Category|string $category = null, Category|string $subcategory = null)
    {
        //dd($category, $subcategory);
        $ids      = [];
        $response = collect();

        if ($category) {
            if (is_string($category)) {
                $category = Category::query()->whereHas('translation', function ($query) use ($category) {
                    $query->where('slug', $category);
                })->first();

                if ($category) {
                    $ids[] = $category->id;
                }

            } else {
                $ids[] = $category->id;
            }

            if (isset($category->id)) {
                foreach ($category->subcategories()->get() as $subcat) {
                    $ids[] = $subcat->id;
                }
            }
        }

        if ($subcategory) {
            $ids = [];

            if (is_string($subcategory)) {
                $subcategory = Category::query()->whereHas('translation', function ($query) use ($subcategory) {
                    $query->where('slug', $subcategory);
                })->first();

                if ($subcategory) {
                    $ids[] = $subcategory->id;
                }

            } else {
                if (isset($subcategory->id)) {
                    $ids[] = $subcategory->id;
                }
            }
        }

        if ( ! empty($ids)) {
            try {
                $response = self::query()->whereIn('category_id', $ids)->get();

            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
        }

        return $response;
    }

}
