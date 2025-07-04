<?php

namespace App\Models\Front\Catalog;

use App\Helpers\Currency;
use App\Helpers\ProductHelper;
use App\Helpers\Special;
use App\Models\Back\Catalog\Product\ProductAction;
use App\Models\Back\Catalog\Product\ProductCombo;
use App\Models\Back\Catalog\Product\ProductSlug;
use App\Models\Back\Marketing\Review;
use App\Models\Back\Settings\Settings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class Product extends Model
{

    /**
     * @var string
     */
    protected $table = 'products';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var string[]
     */
    protected $appends = [
        'name',
        'description',
        'url',
        'eur_price',
        'eur_special',
        'main_price',
        'main_price_text',
        'main_special',
        'main_special_text',
        'secondary_price',
        'secondary_price_text',
        'secondary_special',
        'secondary_special_text',
        'stars',
        'combo_set',
        'alt'
    ];

    /**
     * @var
     */
    protected $eur;

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

        $this->locale = session('locale');
    }


    /**
     * @param $locale
     *
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    public function getLocalizedRouteKey()
    {
        return $this->translation(current_locale())->slug;
    }


    /**
     * @param $value
     * @param $field
     *
     * @return Model|never|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return static::whereHas('translation', function ($query) use ($value) {
            $query->where('slug', $value);
        })->first() ?? $this->checkSlug($value);;
    }


    private function checkSlug(string $slug)
    {
        $check_slug = ProductSlug::query()->where('slug', $slug)->where('lang', current_locale())->first();

        if ($check_slug) {
            $prod = self::query()->where('id', $check_slug->product_id)->first();

            if ($prod) {
                //dd($prod->url);
                //return route('catalog.route', ['group' => 'kategorija-proizvoda', 'cat' => $prod->category(), 'subcat' => $prod->subcategory(), 'prod' => $prod]);
                //return redirect($prod->url, 301);
                return $prod;
            }
        }

        return abort(404);
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
            return $this->hasOne(ProductTranslation::class, 'product_id')->where('lang', $lang)->first();
        }

        if ($all) {
            return $this->hasMany(ProductTranslation::class, 'product_id');
        }

        return $this->hasOne(ProductTranslation::class, 'product_id')->where('lang', $this->locale);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function combos()
    {
        return $this->hasMany(ProductCombo::class, 'product_id')->where('products', '!=', '[]')->orderBy('sort_order');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getComboSetAttribute()
    {
        if ($this->combo) {
            $response = [];

            foreach ($this->combos()->get() as $combo) {
                $preset_session = null;
                $prod_response  = [];
                $session        = session('combo.' . $this->id);
                $products       = Product::query()->whereIn('id', json_decode($combo->products, true))->available()->active()->get();

                foreach ($products as $key => $product) {
                    $selected = false;

                    if ( ! isset($session[$combo->id]) && ! $key) {
                        $selected = true;
                        // napravi session
                        $preset_session[$combo->id] = $product->id;
                    }
                    if (isset($session[$combo->id]) && ($session[$combo->id] == $product->id)) {
                        $selected = true;
                    }

                    $prod_response[] = [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'image'    => asset($product->image),
                        'selected' => $selected
                    ];
                }

                $response[$combo->id] = [
                    'group'    => $combo->group,
                    'title'    => $combo->value['title'][current_locale()],
                    'products' => $prod_response
                ];
            }

            return $response;
        }

        return null;
    }


    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->translation->name;
    }


    /**
     * @return string
     */
    public function getDescriptionAttribute()
    {
        return $this->translation->description;
    }


    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->translation->url;
        //return ProductHelper::url(\App\Models\Back\Catalog\Product\Product::query()->find($this->id), $this->category());
    }


    /**
     * @return Collection|string
     */
    public function getMainPriceAttribute()
    {
        return Currency::main($this->price);
    }


    /**
     * @return Collection|string
     */
    public function getMainPriceTextAttribute()
    {
        return Currency::main($this->price, true);
    }


    /**
     * @return Collection|string
     */
    public function getMainSpecialAttribute()
    {
        return Currency::main($this->special());
    }


    /**
     * @return Collection|string
     */
    public function getMainSpecialTextAttribute()
    {
        return Currency::main($this->special(), true);
    }


    /**
     * @return Collection|string
     */
    public function getSecondaryPriceAttribute()
    {
        return Currency::secondary($this->price);
    }


    /**
     * @param $value
     *
     * @return array|string|string[]
     */
    public function getStarsAttribute($value)
    {
        return $this->reviews()->avg('stars');
    }


    /**
     * @return Collection|string
     */
    public function getSecondaryPriceTextAttribute()
    {
        return Currency::secondary($this->price, true);
    }


    /**
     * @return Collection|string
     */
    public function getSecondarySpecialAttribute()
    {
        return Currency::secondary($this->special());
    }


    /**
     * @return Collection|string
     */
    public function getSecondarySpecialTextAttribute()
    {
        return Currency::secondary($this->special(), true);
    }


    /**
     * @return string
     */
    public function getEurPriceAttribute()
    {
        $this->eur = Settings::get('currency', 'list')->where('code', 'EUR')->first();

        if (isset($this->eur->status) && $this->eur->status) {
            return number_format(($this->price * $this->eur->value), 2);
        }

        return null;
    }


    /**
     * @return string
     */
    public function getEurSpecialAttribute()
    {
        $this->eur = Settings::get('currency', 'list')->where('code', 'EUR')->first();

        if (isset($this->eur->status) && $this->eur->status) {
            return number_format(($this->special() * $this->eur->value), 2);
        }

        return null;
    }


    /**
     * @param $value
     *
     * @return array|string|string[]
     */
    public function getImageAttribute($value)
    {
        return config('settings.images_domain') . str_replace('.jpg', '.webp', $value);
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


    public function getAltAttribute($value)
    {
        $search = str_replace(config('app.url'), '', $this->image);
        $search = str_replace('.webp', '.jpg', $search);
        $image  = ProductImage::query()->where('image', $search)->with('translation')->first();

        if ($image) {
            return [
                'title' => $image->translation->title,
                'alt'   => $image->translation->alt,
            ];
        }

        return [
            'title' => null,
            'alt'   => null,
        ];
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->where('published', 1)->orderBy('sort_order');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->where('status', 1)->orderBy('sort_order');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function action()
    {
        return $this->hasOne(ProductAction::class, 'id', 'action_id')->where('status', 1);
    }


    /**
     * @param $ocjena
     * @param $total
     *
     * @return float|void
     */
    public function percentreviews($ocjena, $total)
    {
        if ($total) {
            return round(($ocjena / $total) * 100, 2);
        }

        return 0;
    }


    /**
     * @return float|int|null
     */
    public function special(): float|int
    {
        $special   = new Special($this);
        $action    = $special->resolveAction();
        $coupon_ok = $special->checkCoupon($action);
        $dates_ok  = $special->checkDates($action);

        if ($coupon_ok && $dates_ok) {
            return $special->getDiscountPrice($action);
        }

        return $this->price;
    }


    /**
     * @return string
     */
    public function coupon(): bool
    {
        $special = new Special($this);
        $action  = $special->resolveAction();

        return $special->checkCoupon($action);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(Author::class, 'id', 'author_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function publisher()
    {
        return $this->hasOne(Publisher::class, 'id', 'publisher_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function categories()
    {
        return $this->hasManyThrough(Category::class, CategoryProducts::class, 'product_id', 'id', 'id', 'category_id');
    }


    /**
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasOneThrough|\Illuminate\Database\Query\Builder|mixed|object|null
     */
    public function category()
    {
        return $this->hasOneThrough(Category::class, CategoryProducts::class, 'product_id', 'id', 'id', 'category_id')
                    ->where('parent_id', 0)
                    ->first();
    }


    /**
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasOneThrough|\Illuminate\Database\Query\Builder|mixed|object|null
     */
    public function subcategory()
    {
        return $this->hasOneThrough(Category::class, CategoryProducts::class, 'product_id', 'id', 'id', 'category_id')
                    ->where('parent_id', '!=', 0)
                    ->first();
    }


    /**
     * @return string
     */
    public function priceString(string $price = null)
    {
        if ($price) {
            $set = explode('.', $price);

            if ( ! isset($set[1])) {
                $set[1] = '00';
            }

            return number_format($price, 0, '', '.') . ',' . substr($set[1], 0, 2) . ' €';
        }

        $set = explode('.', $this->price);

        return number_format($this->price, 0, '', '.') . ',' . substr($set[1], 0, 2) . ' €';
    }


    /**
     * @param int $id
     *
     * @return mixed
     */
    public function tax(int $id)
    {
        return Settings::get('tax', 'list')->where('id', $id)->first();
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeOnAction($query)
    {
        $actions = ProductAction::active()->pluck('product_id');

        if ($actions->count() < 8) {
            $count = 8 - $actions->count();

            for ($i = 0; $i < $count; $i++) {
                $product = Product::whereNotIn('id', $actions)->inRandomOrder()->limit(1)->pluck('id');
                $actions->push($product[0]);
            }
        }

        return $query->whereIn('id', $actions)->with('action');
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1)->where('price', '!=', 0);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 0);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeHasStock(Builder $query): Builder
    {
        return $query->where('quantity', '!=', 0);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeLast(Builder $query, $count = 12): Builder
    {
        return $query->where('status', 1)->orderBy('created_at', 'desc')->limit($count);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeCreated($query, $count = 9)
    {
        return $query->where('status', 1)->orderBy('created_at', 'desc')->limit($count);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('quantity', '!=', 0);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopePopular(Builder $query, $count = 12): Builder
    {
        return $query->where('status', 1)->orderBy('viewed', 'desc')->limit($count);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeTopPonuda(Builder $query, $count = 12): Builder
    {
        return $query->where('status', 1)->where('topponuda', 1)->orderBy('updated_at', 'desc')->limit($count);
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeBasicData(Builder $query): Builder
    {
        return $query->select('id', 'name', 'url', 'image', 'price', 'special', 'author_id');
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @param Request         $request
     * @param Collection|null $ids
     *
     * @return Builder
     */
    public function filter(Request $request, Collection $ids = null): Builder
    {
        $query = $this->newQuery();

        //$query->active()->hasStock();
        $query->active()->hasStock();

        if ($ids && $ids->count() && ! \request()->has('pojam')) {
            $query->whereIn('id', $ids->unique());
        }

        if ($request->has('ids') && $request->input('ids') != '') {
            $_ids = explode(',', substr($request->input('ids'), 1, -1));
            $query->whereIn('id', collect($_ids)->unique());
        }

        if ($request->has('group')) {
            // Akcije
            if ($request->input('group') == 'snizenja') {
                /*$query->where('special', '!=', '')
                    ->where(function ($query) {
                        $query->whereDate('special_from', '<=', now())->orWhereNull('special_from');
                    })
                    ->where(function ($query) {
                        $query->whereDate('special_to', '>=', now())->orWhereNull('special_to');
                    });*/
            } else {
                // Kategorija...
                $group = $request->input('group');

                $query->whereHas('categories', function ($query) use ($request, $group) {
                    $query->where('group', 'like', '%' . $group . '%');
                });
            }
        }

        if ($request->has('cat')) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('category_id', $request->input('cat'));
            });
        }

        if ($request->has('subcat')) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('category_id', $request->input('subcat'));
            });
        }

        if ($request->has('brand')) {
            $auts = [];

            if (is_array($request->input('brand'))) {
                foreach ($request->input('brand') as $key => $item) {
                    if (isset($item->id)) {
                        array_push($auts, $item->id);
                    } else {
                        array_push($auts, $key);
                    }
                }
            }

            if (is_string($request->input('brand'))) {
                $value = $request->input('brand');
                $brand = Brand::query()->whereHas('translation', function ($query) use ($value) {
                    $query->where('slug', $value);
                })->first();

                if ($brand) {
                    array_push($auts, $brand->id);
                }
            }

            $query->whereIn('brand_id', $auts);
        }

        if ($request->has('nakladnik')) {
            $pubs = [];

            foreach ($request->input('nakladnik') as $key => $item) {
                if (isset($item->id)) {
                    array_push($pubs, $item->id);
                } else {
                    array_push($pubs, $key);
                }
            }

            $query->whereIn('publisher_id', $pubs);
        }

        if ($request->has('start')) {
            $query->where(function ($query) use ($request) {
                $query->where('year', '>=', $request->input('start'))->orWhereNull('year');
            });
        }

        if ($request->has('end')) {
            $query->where(function ($query) use ($request) {
                $query->where('year', '<=', $request->input('end'))->orWhereNull('year');
            });
        }

        if ($request->has('sort')) {
            $sort = $request->input('sort');

            if ($sort == 'novi') {
                $query->orderBy('created_at', 'desc');
            }

            if ($sort == 'price_up') {
                $query->orderBy('price');
            }

            if ($sort == 'price_down') {
                $query->orderBy('price', 'desc');
            }

            if ($sort == 'naziv_up') {
                $query->orderBy(ProductTranslation::query()->select('name')->whereColumn('product_translations.product_id', 'products.id')->where('product_translations.lang', current_locale()));
            }

            if ($sort == 'naziv_down') {
                $query->orderByDesc(ProductTranslation::query()->select('name')->whereColumn('product_translations.product_id', 'products.id')->where('product_translations.lang', current_locale()));
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }


    public function resolveComboSession()
    {
        $session_key = 'combo.' . $this->id;
    }


    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/
    // Static functions

    /**
     * @return mixed
     */
    public static function getMenu()
    {
        return self::where('status', 1)->select('id', 'name')->get();
    }


    /**
     * Return the list usually for
     * select or autocomplete html element.
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function list()
    {
        $query = (new self())->newQuery();

        return $query->where('status', 1)->select('id', 'name')->get();
    }

}
