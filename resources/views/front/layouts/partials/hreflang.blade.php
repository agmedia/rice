@if (isset($langs))
    @foreach ($langs as $lang)
        <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang['code'], $lang['slug'], [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
    @endforeach
@else
    @foreach (ag_lang() as $lang )
        @if (isset($page) && $page->id == 5 )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('index'), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
        @endif
        @if (isset($blog) && !$frontblogs )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.blog', ['cat' => $blog->translation($lang->code)->slug]), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
        @endif
        @if (isset($blog) && $frontblogs )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.blog'), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
        @endif
        @if (isset($recepti) && !$receptin )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.recepti', ['cat' => $recepti->translation($lang->code)->slug]),[], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
        @endif
        @if (isset($recepti) && $receptin )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.recepti'), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
        @endif
        @if (isset($brand)  )


            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.brand', ['brand' => $brand->translation->slug]), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />


        @endif
        @if (isset($page) && $page->id != 5)
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.page', ['page' => $page->translation($lang->code)->slug]), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
        @endif
        @if (isset($group) && isset($cat) && ! $cat)
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path'))]), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
        @endif
        @if (isset($cat) && $cat && ! $subcat && ! $prod)
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug])) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug])) }}" hreflang="x-default" />
            @endif
        @endif
        @if (isset($subcat) && $subcat && ! $prod)
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug, 'subcat' => $subcat->translation($lang->code)->slug]), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
        @endif
        @if (isset($prod) && $prod)
            @if (isset($cat) && $cat && ! $subcat)
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug, 'subcat' => $prod->translation($lang->code)->slug]), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
            @else
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug, 'subcat' => $prod->translation($lang->code)->slug]), [], true) }}" hreflang="{{ Str::lower($lang->code) }}-HR" />
            @endif
        @endif
    @endforeach
@endif

