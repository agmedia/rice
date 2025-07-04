<?php

namespace App\Models\Back\Catalog\Product;

use App\Helpers\ProductHelper;
use App\Models\Back\Catalog\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Bouncer;

class ProductTranslation extends Model
{

    /**
     * @var string
     */
    protected $table = 'product_translations';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at'];


    /**
     * @param int     $id
     * @param Request $request
     *
     * @return bool
     */
    public static function create(int $id, Request $request): bool
    {
        foreach (ag_lang() as $lang) {
            $slug = static::resolveSlug($id, $request, $lang->code);

            $saved = self::insertGetId([
                'product_id'       => $id,
                'lang'             => $lang->code,
                'name'             => $request->name[$lang->code],
                'description'      => ProductHelper::cleanHTML($request->description[$lang->code]),
                'podaci'           => ProductHelper::cleanHTML($request->podaci[$lang->code]),
                'sastojci'         => ProductHelper::cleanHTML($request->sastojci[$lang->code]),
                'meta_title'       => $request->meta_title[$lang->code],
                'meta_description' => $request->meta_description[$lang->code],
                'slug'             => $slug,
                'url'              => '',//static::resolveURL($id, $lang->code),
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now()
            ]);

            if ( ! $saved) {
                return false;
            }

        }

        return true;
    }


    /**
     * @param int     $id
     * @param Request $request
     *
     * @return bool
     */
    public static function edit(int $id, Request $request): bool
    {
        foreach (ag_lang() as $lang) {
            $slug = static::resolveSlug($id, $request, $lang->code, 'update');

            $saved = self::where('product_id', $id)->where('lang', $lang->code)->update([
                'name'             => $request->name[$lang->code],
                'description'      => ProductHelper::cleanHTML($request->description[$lang->code]),
                'podaci'           => ProductHelper::cleanHTML($request->podaci[$lang->code]),
                'sastojci'         => ProductHelper::cleanHTML($request->sastojci[$lang->code]),
                'meta_title'       => $request->meta_title[$lang->code],
                'meta_description' => $request->meta_description[$lang->code],
                'slug'             => $slug,
                'url'              => static::resolveURL($id, $lang->code),
                'updated_at'       => Carbon::now()
            ]);

            if ( ! $saved) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param int $id
     *
     * @return bool
     */
    public static function updateURL(int $id)
    {
        foreach (ag_lang() as $lang) {
            $saved = self::where('product_id', $id)->where('lang', $lang->code)->update([
                'url'        => static::resolveURL($id, $lang->code),
                'updated_at' => Carbon::now()
            ]);

            if ( ! $saved) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param string       $target
     * @param Request|null $request
     *
     * @return string
     */
    private static function resolveSlug(int $id, Request $request, string $lang, string $target = 'insert'): string
    {
        $slug = isset($request->slug[$lang]) ? Str::slug($request->slug[$lang]) : Str::slug($request->name[$lang]);

        if ($target == 'update') {
            $product = Product::query()->where('id', $id)->first();

            if ($product) {
                $slug_check = $product->translation($lang)->slug;

                //dd($request->toArray(), $product->translation($lang)->slug, Str::slug($request->slug[$lang]));

                if ($slug_check != Str::slug($request->slug[$lang])) {
                    ProductSlug::create($slug_check, $lang, $product->id);
                }
            }
        }

        $exist = Product::query()->whereHas('translation', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->count();

        $slug_exist = ProductSlug::query()->where('slug', $slug)->where('lang', $lang)->count();

        $cat_exist = Category::query()->whereHas('translation', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->count();

        if (($cat_exist || $exist > 1 || $slug_exist) && $target == 'update') {
            return $slug . '-' . time();
        }

        if (($cat_exist || $exist || $slug_exist) && $target == 'insert') {
            return $slug . '-' . time();
        }

        return $slug;
    }


    /**
     * @param int     $id
     * @param Request $request
     * @param string  $lang
     *
     * @return string
     */
    public static function resolveURL(int $id, string $lang): string
    {
        $product = Product::query()->find($id);

        if ($product) {
            return ProductHelper::url($product, null, null, $lang);
        }

        return '';
    }

}
