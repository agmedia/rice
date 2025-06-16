@extends('front.layouts.app')



@if (isset($group) && $group)
    @if ($group && ! $cat && ! $subcat)
        @section ( 'title',  \Illuminate\Support\Str::ucfirst($group) )
    @endif
    @if ($cat && ! $subcat)
        @section ( 'title',  $cat->translation->title  )
        @section ( 'description', $cat->translation->meta_description )

        @push('meta_tags')
            <link rel="canonical" href="{{ env('APP_URL')}}kategorija-proizvoda/{{ $cat->translation['slug'] }}" />
        @endpush


    @elseif ($cat && $subcat)


        @section ( 'title', $subcat->translation->meta_title  )
        @section ( 'description',  $subcat->translation->meta_description )

        @push('meta_tags')
            <link rel="canonical" href="{{ env('APP_URL')}}kategorija-proizvoda/{{ $cat->translation['slug'] }}/{{ $subcat->translation['slug'] }}" />
        @endpush

    @endif
@endif

@if (isset($author) && $author)
    @section ('title',  $seo->translation['title'])
    @section ('description', $seo->translation['description'])


    @push('meta_tags')
        <link rel="canonical" href="{{ env('APP_URL')}}{{ $author->translation['url'] }}" />
    @endpush

@endif

@if (isset($publisher) && $publisher)
    @section ('title',  $seo->translation['title'])
    @section ('description', $seo->translation['description'])
    @push('meta_tags')
        <link rel="canonical" href="{{ env('APP_URL')}}{{ $publisher->translation['url'] }}" />
    @endpush
@endif


@if (isset($brand) && $brand)


    @section ('title',  $brand->translation->title)
    @section ('description', $brand->translation->descriptiom)
    @push('meta_tags')
        <link rel="canonical" href="{{ route('catalog.route.brand', ['brand' => $brand->translation->slug]) }}" />
    @endpush
@endif

@if (isset($meta_tags))
    @push('meta_tags')

        @foreach ($meta_tags as $tag)
            <meta name={{ $tag->translation['name'] }} content={{ $tag->translation['content'] }}>
        @endforeach
    @endpush
@endif

@push('meta_tags')
@include('front.layouts.partials.hreflang')
@endpush


@section('content')

    @if (Route::currentRouteName() == 'pretrazi')
        <section class="d-md-flex justify-content-between align-items-center mb-2 pb-2">
            <h1 class="h2 mb-2 mb-md-0 me-3"><span class="small fw-light me-2">{{ __('front/ricekakis.rezultati') }}:</span> {{ request()->input('pojam') }}</h1>
        </section>
    @endif


    @if (isset($brand) && $brand)
        <nav class="mb-4" aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap">
                <li class="breadcrumb-item"><a class="text-nowrap" href="{{ route('index') }}"><i class="ci-home"></i>{{ __('front/ricekakis.homepage') }}</a></li>
                <li class="breadcrumb-item text-nowrap active" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route.brand') }}">Brands</a></li>
                @if ( ! $cat && ! $subcat)
                    <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $brand->title }}</li>
                @endif
                @if ($cat && ! $subcat)
                    <li class="breadcrumb-item text-nowrap active" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route.abrand', ['brand' => $brand]) }}">{{ $brand->title }}</a></li>
                    <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $cat->title }}</li>
                @elseif ($cat && $subcat)
                    <li class="breadcrumb-item text-nowrap active" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route.brand', ['brand' => $brand]) }}">{{ $brand->title }}</a></li>
                    <li class="breadcrumb-item text-nowrap active" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route.brand', ['brand' => $brand, 'cat' => $cat]) }}">{{ $cat->title }}</a></li>
                    <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $subcat->title }}</li>
                @endif
            </ol>
        </nav>
        <section class="d-md-flex justify-content-between align-items-center mb-2 pb-2">
            <h1 class="h2 mb-2 mb-md-0 me-3">{{ $brand->title }}</h1>
        </section>
    @endif


    @if (isset($group) && $group)

        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap">
                <li class="breadcrumb-item"><a class="text-nowrap" href="{{ route('index') }}"><i class="ci-home"></i>{{ __('front/ricekakis.homepage') }}</a></li>
                @if ($group && ! $cat && ! $subcat)
                    <!-- <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ \Illuminate\Support\Str::ucfirst($group) }}</li> -->
                @elseif ($group && $cat)
                    <!--    <li class="breadcrumb-item text-nowrap active" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $group]) }}">{{ \Illuminate\Support\Str::ucfirst($group) }}</a></li>-->
                @endif
                @if ($cat && ! $subcat)
                    <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $cat->translation->title }}</li>
                @elseif ($cat && $subcat)
                    <li class="breadcrumb-item text-nowrap active" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $group, 'cat' => $cat->translation->slug]) }}">{{ $cat->translation->title }}</a></li>
                    <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $subcat->translation->title }}</li>
                @endif
            </ol>
        </nav>

        <section class="py-2 mb-1">
            @if ($group && ! $cat && ! $subcat)
                <h1 class="h2 mb-4 me-3">{{ __('front/ricekakis.web_shop') }}</h1>
                <div class="row">
                    @foreach ($list as $item)
                        <!-- Product-->
                        <div class="article col-md-3 mb-grid-gutter">
                            <a class="card border-0 shadow" href="{{ route('catalog.route', ['group' => $group]) }}/{{ $item['slug'] }}">
                                <img class="card-img-top p-3" loading="lazy" width="200" height="200" src="{{ $item['image'] }}" alt="Kategorija {{ $item['title'] }}">
                                <div class="card-body py-2 text-center px-0">
                                    <h3 class="h4 mt-1 font-title text-primary">{{ $item['title'] }}</h3>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($cat && ! $subcat)
                <h1 class="h2 mb-2 mb-md-0 me-3">{{ $cat->translation->title }}</h1>
                @if($cat->translation->short_description)
                    <p class="mb-0 mt-2"> {{ $cat->translation->short_description }}</p>
               @endif
            @elseif ($cat && $subcat)
                <h1 class="h2 mb-2 mb-md-0 me-3">{{ $subcat->translation->title }}</h1>
                    @if($subcat->translation->short_description)
                      <p class="mb-0 mt-2"> {{ $subcat->translation->short_description }}</p>
                   @endif
            @endif
        </section>

        @if ($cat && ! $subcat)
            @if ($cat->subcategories()->count())
                <section class="py-2 mb-1">
                    <div class="tns-carousel">
                        <div class="tns-carousel-inner" data-carousel-options='{"items": 2, "controls": true, "autoHeight": false, "responsive": {"0":{"items":2, "gutter": 10},"480":{"items":2, "gutter": 10},"800":{"items":4, "gutter": 20}, "1300":{"items":5, "gutter": 30}, "1800":{"items":6, "gutter": 30}}}'>
                            @foreach ($cat->subcategories as $item)
                                <!-- Product-->
                                <div class="article mb-grid-gutter">
                                    <a class="card border-0 shadow" href="{{ route('catalog.route', ['group' => $group, 'cat' => $cat->translation->slug, 'subcat' => $item->translation->slug]) }}">
                                        <img class="card-img-top p-3" loading="lazy" width="200" height="200" src="{{ $item['image'] }}" alt="{{ $item->translation->image_alt }}">
                                        <div class="card-body py-2 text-center px-0">
                                            <h3 class="h4 mt-1 font-title text-primary">{{ $item->translation->title }}</h3>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @endif
    @endif


    <products-view ids="{{ isset($ids) ? $ids : null }}"
                   group="{{ isset($group) ? $group : null }}"
                   cat="{{ isset($cat) ? $cat['id'] : null }}"
                   subcat="{{ isset($subcat) ? $subcat['id'] : null }}"
                   author="{{ isset($author) ? $author['slug'] : null }}"
                   brand="{{ isset($brand) ? $brand->translation->slug : null }}"
                   publisher="{{ isset($publisher) ? $publisher['slug'] : null }}">
    </products-view>


    @if (isset($author) && $author && ! empty($author->description))
        <section class="col">
            <div class="card p2-5 border-0 mt-5 shadow mb-5" >
                <div class="card-body py-md-4 py-3 px-4 ">
                    {!!$author->description !!}
                </div>
            </div>
        </section>
    @endif

    @if (isset($brand) && $brand && ! empty($brand->description))
        <section class="col">
            <div class="card p2-5 border-0 mt-5 shadow mb-5" >
                <div class="card-body py-md-4 py-3 px-4 ">
                    {!!$brand->translation->description !!}
                </div>
            </div>
        </section>
    @endif

    @if ($cat && !$subcat && $cat->translation->description)
        <section class="col">
            <div class="card p2-5 border-0 mt-5 shadow mb-5" >
                <div class="card-body py-md-4 py-3 px-4 ">

                    {!! $cat->translation->description !!}
                </div>
            </div>
        </section>
    @elseif ($subcat && $subcat->translation->description)
        <section class="col">
            <div class="card p2-5 border-0 mt-5 shadow mb-5" >
                <div class="card-body py-md-4 py-3 px-4 ">

                    {!! $subcat->translation->description !!}
                </div>
            </div>
        </section>
    @endif



    @if(isset($faqs) and !$faqs->isEmpty())
        <section class="col">
            <div class="card p2-5 border-0 mt-5 shadow mb-5" >
                <div class="card-body py-md-4 py-3 px-4 ">

                    <h2> {{ __('front/cart.faq') }}</h2>

                    <div class="rounded-3 p-2 mt-3" style="border: 1px solid rgb(218, 225, 231); background-color: rgb(255, 255, 255) !important;">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            @foreach ($faqs as $fa)
                                <!-- Item -->
                                <div class="accordion-item">
                                    <h3 class="accordion-header" id="flush-heading{{ $fa->id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{ $fa->id }}" aria-expanded="false" aria-controls="flush-collapse{{ $fa->id }}">{{ $fa->title }}</button>
                                    </h3>
                                    <div class="accordion-collapse collapse" id="flush-collapse{{ $fa->id }}" aria-labelledby="flush-heading{{ $fa->id }}" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">  {!! $fa->description !!}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

@endsection

@push('js_after')
    @if (isset($crumbs))
        <script type="application/ld+json">
            {!! collect($crumbs)->toJson() !!}
        </script>
    @endif

    @if(isset($faqs) and !$faqs->isEmpty())
    <script type="application/ld+json">
        {!! collect($faqs_crumbs)->toJson() !!}
    </script>
    @endif
@endpush

@push('js_after')
    <style>
        @media only screen and (max-width: 1040px) {
            .scrolling-wrapper {
                overflow-x: scroll;
                overflow-y: hidden;
                white-space: nowrap;
                padding-bottom: 15px;
            }
        }
    </style>
@endpush
