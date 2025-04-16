<?php

namespace App\Http\Controllers\Back\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Brand;
use App\Models\Back\Catalog\Category;
use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Catalog\Product\ProductAction;
use App\Models\Back\Catalog\Product\ProductCategory;
use App\Models\Back\Catalog\Product\ProductImage;
use App\Models\Back\Catalog\Product\ProductSlug;
use App\Models\Back\Catalog\Publisher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Product $product)
    {
        $query = $product->filter($request);

        $products = $query->paginate(50)->appends(request()->query());

        if ($request->has('status')) {
            if ($request->input('status') == 'with_action' || $request->input('status') == 'without_action') {
                $products = collect();
                $temps = Product::all();

                if ($request->input('status') == 'with_action') {
                    foreach ($temps as $product) {
                        if ($product->special()) {
                            $products->push($product);
                        }
                    }
                }

                if ($request->input('status') == 'without_action') {
                    foreach ($temps as $product) {
                        if ( ! $product->special()) {
                            $products->push($product);
                        }
                    }
                }

                $products = $this->paginateColl($products);
            }
        }

        $categories = (new Category())->getList(false);
        /*$authors    = Author::all()->pluck('title', 'id');
        $publishers = Publisher::all()->pluck('title', 'id');*/
        $counts = [];//Product::setCounts($query);

        return view('back.catalog.product.index', compact('products', 'categories'/*, 'authors', 'publishers'*/, 'counts'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Product();

        $data = $product->getRelationsData();

        return view('back.catalog.product.edit', compact('data'));
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function createCombo()
    {
        $product = new Product();

        $data = $product->getRelationsData(1);

        return view('back.catalog.product.edit', compact('data'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->toArray());
        $product = new Product();

        $stored = $product->validateRequest($request)->create();

        if ($stored) {
            $product->storeImages($stored);

            $route = $product->combo ? 'products.edit.combo' : 'products.edit';

            return redirect()->route($route, ['product' => $stored])->with(['success' => 'Artikl je uspješno snimljen!']);
        }

        return redirect()->back()->with(['error' => 'Ops..! Greška prilikom snimanja.']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $data = $product->getRelationsData();

        //dd($data['slugs']->where('lang', 'hr')->all());

        return view('back.catalog.product.edit', compact('product', 'data'));
    }


    /**
     * @param Product $product
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editCombo(Product $product)
    {
        $data = $product->getRelationsData(1);

      //  dd($product->combos->first()->value);

        return view('back.catalog.product.edit', compact('product', 'data'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Product                  $product
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //dd($request->toArray());
        $updated = $product->validateRequest($request)->edit();

        if ($updated) {
            $product->storeImages($updated);

            $route = $product->combo ? 'products.edit.combo' : 'products.edit';

            return redirect()->route($route, ['product' => $updated])->with(['success' => 'Artikl je uspješno snimljen!']);
        }

        return redirect()->back()->with(['error' => 'Ops..! Greška prilikom snimanja.']);
    }


    /**
     * @param Request     $request
     * @param ProductSlug $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSlug(Request $request, ProductSlug $slug)
    {
        $slug->delete();

        return redirect()->back()->with(['success' => 'SEO Url je uspješno obrisan!']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Product $product)
    {
        ProductImage::where('product_id', $product->id)->delete();
        ProductCategory::where('product_id', $product->id)->delete();

        Storage::deleteDirectory(config('filesystems.disks.products.root') . $product->id);

        $destroyed = Product::destroy($product->id);

        if ($destroyed) {
            return redirect()->route('products')->with(['success' => 'Artikl je uspješno snimljen!']);
        }

        return redirect()->back()->with(['error' => 'Ops..! Greška prilikom snimanja.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyApi(Request $request)
    {
        if ($request->has('id')) {
            $id = $request->input('id');

            ProductImage::where('product_id', $id)->delete();
            ProductCategory::where('product_id', $id)->delete();

            Storage::deleteDirectory(config('filesystems.disks.products.root') . $id);

            $destroyed = Product::destroy($id);

            if ($destroyed) {
                return response()->json(['success' => 200]);
            }
        }

        return response()->json(['error' => 300]);
    }


    /**
     * @param       $items
     * @param int   $perPage
     * @param null  $page
     * @param array $options
     *
     * @return LengthAwarePaginator
     */
    public function paginateColl($items, $perPage = 50, $page = null, $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
