<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Breadcrumb;
use App\Helpers\Helper;
use App\Helpers\Metatags;
use App\Helpers\ProductHelper;
use App\Helpers\RouteResolver;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontBaseController;
use App\Imports\ProductImport;
use App\Models\Back\Catalog\Product\ProductSlug;
use App\Models\Back\Settings\Settings;
use App\Models\Front\Blog;
use App\Models\Front\Recepti;
use App\Models\Front\Page;
use App\Models\Front\Faq;
use App\Models\Front\Catalog\Author;
use App\Models\Front\Catalog\Category;
use App\Models\Front\Catalog\Product;
use App\Models\Front\Catalog\Publisher;
use App\Models\Front\Catalog\Brand;
use App\Models\Seo;
use App\Models\TagManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CatalogRouteController extends FrontBaseController
{

    /**
     * Resolver for the Groups, categories and products routes.
     * Route::get('{group}/{cat?}/{subcat?}/{prod?}', 'Front\GCP_RouteController::resolve()')->name('gcp_route');
     *
     * @param               $group
     * @param Category|null $cat
     * @param Category|null $subcat
     * @param Product|null  $prod
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resolve(Request $request, $group, Category $cat = null, $subcat = null, Product $prod = null)
    {
        //dd($request->toArray(), $group, $cat, $subcat, $prod);
        //dd($request->all(), $request->path(), $request->url(), $request->getRequestUri());
        //
        if ($subcat) {
            $sub_category = Category::whereHas('translation', function ($query) use ($subcat) {
                $query->where('slug', $subcat);
            })->where('parent_id', $cat->id)->first();

            if ( ! $sub_category) {
                $prod = Product::query()->whereHas('translation', function ($query) use ($subcat) {
                    $query->where('slug', $subcat);
                })->first();

                if ( ! $prod) {
                    $prod = $this->checkSlug($subcat);

                    if ($prod) {
                        return redirect($prod->url, 301);
                    }
                }
            }

            $subcat = $sub_category;
        }

        // Check if there is Product set.
        if ($prod) {
            $slug = substr($request->path(), strrpos($request->path(), '/') + 1);
            //dd($slug, $prod->translation->slug, $slug == $prod->translation->slug);
            if ($slug != $prod->translation->slug) {
                return redirect($prod->url, 301);
            }

            if ( ! $prod->status) {
                abort(404);
            }

            if (  $prod->size_value && $prod->size_type) {


                $pricePerKg = (1000 / $prod->size_value) * $prod->price;

                if($prod->size_type = 'g'){

                    $price_per_kg = number_format($pricePerKg, 2). ' €/kg';

                } else{
                    $price_per_kg = number_format($pricePerKg, 2). ' €/l';
                }


            } else{

                $price_per_kg = '';

            }

            $seo     = Seo::getProductData($prod);
            $gdl     = TagManager::getGoogleProductDataLayer($prod);
            $reviews = $prod->reviews()->get();
            $related = Helper::getRelated($cat, $subcat);

            $bc         = new Breadcrumb();
            $crumbs     = $bc->product($group, $cat, $subcat, $prod)->resolve();
            $bookscheme = Metatags::productSchema($prod, $reviews);

            $shipping_methods = Settings::getList('shipping', 'list.%', true);
            $payment_methods  = Settings::getList('payment', 'list.%', true);

            $has_combo_session = 1;

            if ($prod->combo) {
                $has_combo_session = ProductHelper::checkComboProductSession($prod->id);
            }

            $view = view('front.catalog.product.index', compact('prod', 'group', 'cat', 'subcat', 'related', 'seo', 'shipping_methods', 'payment_methods', 'crumbs', 'bookscheme', 'gdl', 'reviews', 'has_combo_session', 'price_per_kg'));

            return response($view)->header('Last-Modified', Carbon::make($prod->updated_at)->toRfc7231String());
        }

        $list = [];
        // If only group and has any category... continue...
        if ($group && ! $cat && ! $subcat) {
            if ( ! Category::where('group', $group)->first('id')) {
                return $this->resolveOldUrl($group);
            }

            $list = Category::where('group', $group)->get();
        }

        if ($cat) {
            $cat->count = Helper::resolveCache('cat')->remember('cpc' . $cat->id, config('cache.life'), function () use ($cat) {
                return $cat->products()->count();
            });
        }
        if ($subcat) {
            $subcat->count = Helper::resolveCache('cat')->remember('scpc' . $subcat->id, config('cache.life'), function () use ($subcat) {
                return $subcat->products()->count();
            });
        }

        $meta_tags = Seo::getMetaTags($request, 'filter');
        $crumbs = (new Breadcrumb())->category($group, $cat, $subcat)->resolve();
        $faqs = Faq::getCategoryList($cat, $subcat);
        $faqs_crumbs = [];

        if ($faqs && $faqs->count()) {
            $faqs_crumbs = (new Breadcrumb())->faqs($faqs)->resolve('mainEntity');
        }

        return view('front.catalog.category.index', compact('group', 'list', 'cat', 'subcat', 'prod', 'crumbs', 'meta_tags', 'faqs', 'faqs_crumbs'));
    }


    private function checkSlug(string $slug)
    {
        $check_slug = ProductSlug::query()->where('slug', $slug)->where('lang', current_locale())->first();

        if ($check_slug) {
            $prod = Product::query()->where('id', $check_slug->product_id)->first();

            if ($prod) {
                return $prod;
            }
        }

        return null;
    }


    /**
     * @param null $prod
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolveOldUrl($prod = null)
    {
        if ($prod) {
            $prod = substr($prod, 0, strrpos($prod, '-'));
            $prod = Product::query()->whereHas('translation', function ($query) use ($prod) {
                $query->where('slug', 'LIKE', $prod . '%');
            })->first();

            if ($prod) {
                //dd(url($prod->translation->url));
                return redirect(url($prod->translation->url));
            }
        }

        abort(404);
    }


    /**
     * @param null $prod
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolveOldCategoryUrl(string $group = null, $cat = null, $subcat = null)
    {
        if ($group) {
            return redirect()->route('catalog.route', ['group' => $group, 'cat' => $cat, 'subcat' => $subcat]);
        }

        abort(404);
    }


    /**
     *
     *
     * @param Author $author
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function author(Request $request, Author $author = null, Category $cat = null, Category $subcat = null)
    {
        if ( ! $author) {
            $letters = Helper::resolveCache('authors')->remember('aut_' . 'letters', config('cache.life'), function () {
                return Author::letters();
            });
            $letter  = 0; //$this->checkLetter($letters);

            if ($request->has('letter')) {
                $letter = $request->input('letter');
            }

            $currentPage = request()->get('page', 1);

            $authors = Helper::resolveCache('authors')->remember('aut_' . $letter . '.' . $currentPage, config('cache.life'), function () use ($letter) {
                $auts = Author::query()->select('id', 'title', 'url')->where('status', 1);

                if ($letter) {
                    $auts->where('letter', $letter);
                }

                return $auts->orderBy('title')
                            ->withCount('products')
                            ->paginate(36)
                            ->appends(request()->query());
            });

            $meta_tags = Seo::getMetaTags($request, 'ap_filter');

            return view('front.catalog.authors.index', compact('authors', 'letters', 'letter', 'meta_tags'));
        }

        $letter = null;

        if ($cat) {
            $cat->count = $cat->products()->count();
        }
        if ($subcat) {
            $subcat->count = $subcat->products()->count();
        }

        $seo = Seo::getAuthorData($author, $cat, $subcat);

        $crumbs = null;

        return view('front.catalog.category.index', compact('author', 'letter', 'cat', 'subcat', 'seo', 'crumbs'));
    }


    /**
     *
     *
     * @param Brand $brand
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function brand(Request $request, Brand $brand = null, Category $cat = null, Category $subcat = null)
    {
        if ( ! $brand) {
            $letters = Helper::resolveCache('brands')->remember('aut_' . 'letters', config('cache.life'), function () {
                return Brand::letters();
            });
            $letter  = 0; //$this->checkLetter($letters);

            if ($request->has('letter')) {
                $letter = $request->input('letter');
            }

            $currentPage = request()->get('page', 1);

            $brands = Helper::resolveCache('brands')->remember('aut_' . $letter . '.' . $currentPage, config('cache.life'), function () use ($letter) {
                $auts = Brand::query()->where('status', 1);

                if ($letter) {
                    $auts->where('letter', $letter);
                }

                return $auts->orderBy('id')
                            ->withCount('products')
                            ->paginate(36)
                            ->appends(request()->query());
            });

            $meta_tags = Seo::getMetaTags($request, 'ap_filter');

            return view('front.catalog.brands.index', compact('brands', 'letters', 'letter', 'meta_tags'));
        }

        $letter = null;

        if ($cat) {
            $cat->count = $cat->products()->count();
        }
        if ($subcat) {
            $subcat->count = $subcat->products()->count();
        }

        $seo = Seo::getBrandData($brand, $cat, $subcat);

        $crumbs = null;

        return view('front.catalog.category.index', compact('brand', 'letter', 'cat', 'subcat', 'seo', 'crumbs'));
    }


    /**
     *
     *
     * @param Publisher $publisher
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function publisher(Request $request, Publisher $publisher = null, Category $cat = null, Category $subcat = null)
    {
        if ( ! $publisher) {
            $letters = Helper::resolveCache('publishers')->remember('pub_' . 'letters', config('cache.life'), function () {
                return Publisher::letters();
            });
            $letter  = 0; //$this->checkLetter($letters);

            if ($request->has('letter')) {
                $letter = $request->input('letter');
            }

            $currentPage = request()->get('page', 1);

            $publishers = Helper::resolveCache('publishers')->remember('pub_' . $letter . '.' . $currentPage, config('cache.life'), function () use ($letter) {
                $pubs = Publisher::query()->select('id', 'title', 'url')->where('status', 1);

                if ($letter) {
                    $pubs->where('letter', $letter);
                }

                return $pubs->orderBy('title')
                            ->withCount('products')
                            ->paginate(36)
                            ->appends(request()->query());
            });

            $meta_tags = Seo::getMetaTags($request, 'ap_filter');

            return view('front.catalog.publishers.index', compact('publishers', 'letters', 'letter', 'meta_tags'));
        }

        $letter = null;

        if ($cat) {
            $cat->count = $cat->products()->count();
        }
        if ($subcat) {
            $subcat->count = $subcat->products()->count();
        }

        $seo = Seo::getPublisherData($publisher, $cat, $subcat);

        $crumbs = null;

        return view('front.catalog.category.index', compact('publisher', 'letter', 'cat', 'subcat', 'seo', 'crumbs'));
    }


    /**
     *
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $key = config('settings.search_keyword');

        if ($request->has($key)) {
            if ( ! $request->input($key) || empty($request->input($key))) {
                return redirect()->back()->with(['error' => 'Oops..! Zaboravili ste upisati pojam za pretraživanje..!']);
            }

            $group  = null;
            $cat    = null;
            $subcat = null;

            $ids = Helper::search($request->input($key));

            $crumbs = Metatags::searchSchema($request->getRequestUri(), $request->input($key));

            return view('front.catalog.category.index', compact('group', 'cat', 'subcat', 'ids', 'crumbs'));
        }

        if ($request->has(config('settings.search_keyword') . '_api')) {
            $search = Helper::search(
                $request->input(config('settings.search_keyword') . '_api')
            );

            return response()->json($search);
        }

        return response()->json(['error' => 'Greška kod pretrage..! Molimo pokušajte ponovo ili nas kotaktirajte! HVALA...']);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function actions(Request $request, Category $cat = null, $subcat = null)
    {
        $ids   = Product::query()->whereNotNull('special')->pluck('id');
        $group = 'snizenja';

        $crumbs = null;

        return view('front.catalog.category.index', compact('group', 'cat', 'subcat', 'ids', 'crumbs'));
    }


    /**
     * @param Page $page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function page(Page $page)
    {
        $view = view('front.page', compact('page'));

        return response($view)->header('Last-Modified', Carbon::make($page->updated_at)->toRfc7231String());
    }


    /**
     * @param Blog $blog
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function blog(string $cat = null, string $subcat = null, Blog $blog = null)
    {
        $category    = null;
        $subcategory = null;
        $route       = new RouteResolver('blog', $blog);

        //dd($cat, $subcat, $blog);

        if ( ! $blog || ! $blog->exists) {
            $frontblogs = Blog::query()->where('status', 1);

            if ($cat) {
                $category = $route->getCategory($cat);

                if ( ! $category) {
                    $blog = $route->getModel($cat);
                }

                if ( ! $blog && ! $subcat && $category) {
                    $frontblogs = $route->getModelsByCategory($frontblogs, $category);
                }

                if ($subcat && $category) {
                    $subcategory = $route->getCategory($subcat, $category->id);

                    if ( ! $subcategory) {
                        abort(404);
                    }

                    $frontblogs = $route->getModelsByCategory($frontblogs, $subcategory);
                }
            }

            if ( ! $blog) {
                $frontblogs  = $frontblogs->orderBy('id', 'desc')->get();
                $breadcrumbs = $route->attachBreadcrumbs($category, $subcategory, $blog);
                $faqs = Faq::getCategoryList($cat, $subcat);
                $faqs_crumbs = [];

                if ($faqs && $faqs->count()) {
                    $faqs_crumbs = (new Breadcrumb())->faqs($faqs)->resolve('mainEntity');
                }

                return view('front.blog', compact('frontblogs', 'breadcrumbs', 'category','subcategory', 'faqs', 'faqs_crumbs'));
            }
        }

        $breadcrumbs = $route->attachBreadcrumbs($category, $subcategory, $blog);
        $frontblogs  = null;

        $blog->description = Helper::setDescription($blog->description, $blog->id);
        $blog->schema = Metatags::blogSchema($blog);

        $view = view('front.blog', compact('blog', 'frontblogs', 'breadcrumbs'));

        return response($view)->header('Last-Modified', Carbon::make($blog->updated_at)->toRfc7231String());
    }


    /**
     * @param Recepti $recepti
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function recepti(string $cat = null, string $subcat = null, Recepti $recepti = null)
    {
        $category    = null;
        $subcategory = null;
        $route       = new RouteResolver('recepti', $recepti);

        if ( ! $recepti || ! $recepti->exists) {
            $receptin = Recepti::query()->where('status', 1);

            if ($cat) {
                $category = $route->getCategory($cat);

                if ( ! $category) {
                    $recepti = $route->getModel($cat);
                }

                if ( ! $recepti && ! $subcat && $category) {
                    $receptin = $route->getModelsByCategory($receptin, $category);
                }

                if ($subcat && $category) {
                    $subcategory = $route->getCategory($subcat, $category->id);

                    if ( ! $subcategory) {
                        abort(404);
                    }

                    $receptin = $route->getModelsByCategory($receptin, $subcategory);
                }
            }

            if ( ! $recepti) {
                $receptin    = $receptin->orderBy('id', 'desc')->get();
                $breadcrumbs = $route->attachBreadcrumbs($category, $subcategory, $recepti);
                $faqs = Faq::getCategoryList($cat, $subcat);
                $faqs_crumbs = [];

                if ($faqs && $faqs->count()) {
                    $faqs_crumbs = (new Breadcrumb())->faqs($faqs)->resolve('mainEntity');
                }

                return view('front.recepti', compact('receptin', 'breadcrumbs','category','subcategory', 'faqs', 'faqs_crumbs'));
            }
        }

        $breadcrumbs = $route->attachBreadcrumbs($category, $subcategory, $recepti);
        $receptin    = null;

        $recepti->description = Helper::setDescription($recepti->description, $recepti->id);
        $recepti->schema = Metatags::recipeSchema($recepti);

        $view = view('front.recepti', compact('recepti', 'receptin', 'breadcrumbs'));

        return response($view)->header('Last-Modified', Carbon::make($recepti->updated_at)->toRfc7231String());
    }


    /**
     * @param Faq $faq
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function faq()
    {
        $faq = Faq::where('status', 1)->get();

        return view('front.faq', compact('faq'));
    }


    /**
     * @param array $letters
     *
     * @return string
     */
    private function checkLetter(Collection $letters): string
    {
        foreach ($letters->all() as $letter) {
            if ($letter['active']) {
                return $letter['value'];
            }
        }

        return 'A';
    }

}
