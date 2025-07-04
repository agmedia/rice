<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Helper;
use App\Helpers\Metatags;
use App\Helpers\Recaptcha;
use App\Helpers\Xmlexport;
use App\Http\Controllers\FrontBaseController;
use App\Mail\ContactFormMessage;
use App\Models\Back\Marketing\Review;
use App\Models\Front\Loyalty;
use App\Models\Front\Page;
use App\Models\Front\Newsletter;
use App\Models\Sitemap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Carbon;

class HomeController extends FrontBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = Page::whereHas('translation', function ($query) {
            $query->where('slug', 'homepage');
        })->first();

        $page->translation->description = Helper::setDescription(
            isset($page->translation->description) ? $page->translation->description : '',
            isset($page->id) ? $page->id : 0
        );

        $og_schema = Metatags::indexSchema();
        $index_search_schema = Metatags::homepageSearchActionShema();

        $view = view('front.page', compact('page', 'og_schema', 'index_search_schema'));

        return response($view)->header('Last-Modified', Carbon::make($page->updated_at)->toRfc7231String());
    }


    /**
     * @param Page $page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function page(Page $page)
    {
        return view('front.page', compact('page'));
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function contact(Request $request)
    {
        return view('front.contact');
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendProductComment(Request $request)
    {
        $review = new Review();

        $created_review = $review->validateRequest($request)->create();

        if ($created_review) {
            Loyalty::addPoints(config('settings.loyalty.product_review'), $request->input('product_id'), 'product_review');

            return back()->with(['success' => 'Komentar je uspješno poslan']);
        }

        return back()->with(['error' => 'Whoops..! Greška kod snimanja komentara']);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function newsletter(Request $request)
    {
        $newsletter = new Newsletter();

        $created_newsletter = $newsletter->validateRequest($request)->create();

        if ($created_newsletter) {

            return back()->with(['success' => 'Uspješna prijava']);
        }

        return back()->with(['error' => 'Whoops..! Upišite ispravan email']);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function sendContactMessage(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required',
        ]);

        // Recaptcha
        $recaptcha = (new Recaptcha())->check($request->toArray());

        if ( ! $recaptcha || ! $recaptcha->ok()) {
            return back()->withErrors(['error' => 'ReCaptcha Error! Kontaktirajte administratora!']);
        }

        $message = $request->toArray();

        dispatch(function () use ($message) {
            Mail::to(config('mail.admin'))->send(new ContactFormMessage($message));
        });

        return view('front.contact')->with(['success' => 'Vaša poruka je uspješno poslana.! Odgovoriti ćemo vam uskoro.']);
    }


    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function imageCache(Request $request)
    {
        $src = $request->input('src');

        $cacheimage = Image::cache(function($image) use ($src) {
            $image->make($src);
        }, config('imagecache.lifetime'));

        return Image::make($cacheimage)->response();
    }


    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function thumbCache(Request $request)
    {
        if ( ! $request->has('src')) {
            return asset('media/img/knjiga-detalj.jpg');
        }

        $cacheimage = Image::cache(function($image) use ($request) {
            $width = 400;
            $height = 400;

            if ($request->has('size')) {
                if (strpos($request->input('size'), 'x') !== false) {
                    $size = explode('x', $request->input('size'));
                    $width = $size[0];
                    $height = $size[1];
                }
            } else {
                $width = $request->input('size');
            }

            $image->make($request->input('src'))->resize($width, $height);

        }, config('imagecache.lifetime'));

        return Image::make($cacheimage)->response();
    }


    /**
     * @param Request $request
     * @param null    $sitemap
     *
     * @return \Illuminate\Http\Response
     */
    public function sitemapXML(Request $request, $sitemap = null)
    {
        if ( ! $sitemap) {
            $sm = new Sitemap(config('settings.sitemap'));

            return response()->view('front.layouts.partials.sitemap-index', [
                'items' => $sm->getResponse()
            ])->header('Content-Type', 'text/xml');
        }

        $sm = new Sitemap($sitemap);

        return response()->view('front.layouts.partials.sitemap', [
            'items' => $sm->getSitemap()
        ])->header('Content-Type', 'text/xml');
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function sitemapImageXML()
    {
        $sm = new Sitemap('images');

        return response()->view('front.layouts.partials.sitemap-image', [
            'items' => $sm->getResponse()
        ])->header('Content-Type', 'text/xml');
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function xmlexport(Request $request)
    {
        $xmlexport = new Xmlexport();

        return response()->view('front.layouts.partials.xmlexport', [
            'items' => $xmlexport->getItems()
        ])->header('Content-Type', 'text/xml');
    }

}
