@if (isset($langs))
    @foreach ($langs as $lang)
        <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang['code'], $lang['slug'], [], true) }}" hreflang="{{ Str::lower($lang->code) }}" />
    @endforeach
@else
    @foreach (ag_lang() as $lang )
        @if (isset($page) && $page->id == 5 )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('index')) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('index')) }}" hreflang="x-default" />
            @endif
        @endif
        @if (isset($blog) && !$frontblogs )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.blog', ['cat' => $blog->translation($lang->code)->slug])) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.blog', ['cat' => $blog->translation($lang->code)->slug])) }}"   hreflang="x-default" />
            @endif
        @endif
        @if (isset($blog) && $frontblogs )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.blog')) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.blog')) }}"   hreflang="x-default" />
            @endif
        @endif
        @if (isset($recepti) && !$receptin )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.recepti', ['cat' => $recepti->translation($lang->code)->slug])) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.recepti', ['cat' => $recepti->translation($lang->code)->slug])) }}"  hreflang="x-default" />
            @endif
        @endif
        @if (isset($recepti) && $receptin )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.recepti')) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.recepti')) }}"  hreflang="x-default" />
            @endif
        @endif
        @if (isset($brand)  )
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.brand', ['brand' => $brand->translation->slug])) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.brand', ['brand' => $brand->translation->slug])) }}" hreflang="x-default" />
            @endif
        @endif
        @if (isset($page) && $page->id != 5)
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.page', ['page' => $page->translation($lang->code)->slug])) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.page', ['page' => $page->translation($lang->code)->slug])) }}" hreflang="x-default" />
            @endif
        @endif
        @if (isset($group) && isset($cat) && ! $cat)
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path'))])) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path'))])) }}" hreflang="x-default" />
            @endif
        @endif
        @if (isset($cat) && $cat && ! $subcat && ! $prod)
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug])) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug])) }}" hreflang="x-default" />
            @endif
        @endif
        @if (isset($subcat) && $subcat && ! $prod)
            <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug, 'subcat' => $subcat->translation($lang->code)->slug])) }}" hreflang="{{ Str::lower($lang->code) }}" />
            @if ($lang->code == 'hr')
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug, 'subcat' => $subcat->translation($lang->code)->slug])) }}" hreflang="x-default" />
            @endif
        @endif
        @if (isset($prod) && $prod)
            @if (isset($cat) && $cat && ! $subcat)
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug, 'subcat' => $prod->translation($lang->code)->slug])) }}" hreflang="{{ Str::lower($lang->code) }}" />

                @if ($lang->code == 'hr')
                    <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug, 'subcat' => $prod->translation($lang->code)->slug])) }}" hreflang="x-default" />
                @endif

            @else
                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug, 'subcat' => $subcat->translation($lang->code)->slug, 'prod' => $prod->translation($lang->code)->slug])) }}" hreflang="{{ Str::lower($lang->code) }}" />

                @if ($lang->code == 'hr')
                    <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route', ['group' => \Illuminate\Support\Str::slug(config('settings.group_path')), 'cat' => $cat->translation($lang->code)->slug, 'subcat' => $subcat->translation($lang->code)->slug, 'prod' => $prod->translation($lang->code)->slug])) }}" hreflang="x-default" />
                @endif


            @endif
        @endif
    @endforeach
@endif

