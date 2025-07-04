@extends('front.layouts.app')
@if(isset($receptin))
    @if($category and !$subcategory)
        @section ( 'title', $category->title.' - Rice Kakis | Asian Store' )
        @section ( 'description', $category->translation->meta_description )
        @push('meta_tags')
            <link rel="canonical" href="{{ LaravelLocalization::localizeUrl(route('catalog.route.blog', ['cat' => $category->slug])) }}"/>

            @foreach (ag_lang() as $lang )

                <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.blog', ['cat' => $category->translation($lang->code)->slug])) }}" hreflang="{{ Str::lower($lang->code) }}"/>

                @if ($lang->code == 'hr')
                    <link rel="alternate" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->code, route('catalog.route.blog', ['cat' => $category->translation($lang->code)->slug])) }}"  hreflang="x-default" />
                @endif
            @endforeach

        @endpush
    @elseif ($category and $subcategory)

    @else
        @section ( 'title', __('front/ricekakis.recepti_title')  )
        @section ( 'description', __('front/ricekakis.recepti_text') )
        @push('meta_tags')
            <link rel="alternate" href="https://www.ricekakis.com/recepti/" hreflang="hr" />
            <link rel="alternate" href="https://www.ricekakis.com/en/recipes/" hreflang="en" />
            <link rel="alternate" href="https://www.ricekakis.com/recepti/" hreflang="x-default"/>
        @endpush
    @endif



@else
    @section ( 'title', $recepti->title )
    @section ( 'description', $recepti->translation->meta_description )
    @push('meta_tags')
        <link rel="canonical" href="{{ route('catalog.route.recepti', ['cat' => $recepti]) }}" />
        <meta property="og:locale" content="{{ current_locale() == 'hr' ? 'hr_HR' : 'en_HR' }}" />
        <meta property="og:type" content="product" />
        <meta property="og:title" content="{{ $recepti->title }}" />
        <meta property="og:description" content="{{ $recepti->translation->meta_description  }}" />
        <meta property="og:url" content="{{ route('catalog.route.recepti', ['cat' => $recepti]) }}"  />
        <meta property="og:site_name" content="Rice Kakis | Asian Store" />
        <meta property="og:updated_time" content="{{ $recepti->updated_at  }}" />
        <meta property="og:image" content="{{ asset($recepti->translation->image_alt) }}" />
        <meta property="og:image:secure_url" content="{{ asset($recepti->image) }}" />
        <meta property="og:image:width" content="640" />
        <meta property="og:image:height" content="480" />
        <meta property="og:image:type" content="image/webp" />
        <meta property="og:image:alt" content="{{ $recepti->translation->image_alt }}" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $recepti->title }}" />
        <meta name="twitter:description" content="{{ $recepti->translation->meta_description }}" />
        <meta name="twitter:image" content="{{ asset($recepti->image) }}" />
        @include('front.layouts.partials.hreflang')
    @endpush
@endif


@section('content')
    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb flex-lg-nowrap">
            <li class="breadcrumb-item"><a class="text-nowrap" href="{{ route('index') }}"><i class="ci-home"></i>{{ __('front/ricekakis.homepage') }}</a></li>
            <li class="breadcrumb-item"><a class="text-nowrap" href="{{ route('catalog.route.recepti') }}"><i class="ci-home"></i>{{ __('front/ricekakis.recepti') }}</a></li>
            @if (isset($breadcrumbs))
                @foreach ($breadcrumbs as $breadcrumb)
                    @if ( ! $breadcrumb['active'])
                        <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $breadcrumb['title'] }}</li>
                    @else
                        <li class="breadcrumb-item text-nowrap active"><a class="text-nowrap" href="{{ $breadcrumb['url'] }}"><i class="ci-book"></i>{{ $breadcrumb['title'] }}</a></li>
                    @endif
                @endforeach
            @endif
        </ol>
    </nav>
    <section class="d-md-flex justify-content-between align-items-center mb-4 pb-2">
        @if(isset($receptin))
            @if($category and !$subcategory)
                <h1 class="h2 mb-3 mb-md-0 me-3">{{ $category->title}}</h1>
            @else
                <h1 class="h2 mb-3 mb-md-0 me-3">{{ __('front/ricekakis.recepti') }}</h1>
            @endif
        @else
            <h1 class="h2 mb-3 mb-md-0 me-3">{{ $recepti->title }}</h1>
        @endif
    </section>

    @if(isset($receptin))
        <div class=" pb-5 mb-2 mb-md-4">
            <!-- Entries grid-->
            <div class="masonry-grid" data-columns="3" >
                @foreach ($receptin as $recepti)
                    <article class="masonry-grid-item">
                        <div class="card">
                            <a class="blog-entry-thumb" href="{{ route('catalog.route.recepti', ['cat' => $recepti]) }}"><span class="blog-entry-meta-label fs-sm"><i class="ci-pot"></i></span><img class="card-img-top" src="{{ $recepti->image }}" alt="{{ $recepti->translation->image_alt }}"></a>
                            <div class="card-body">
                                <h2 class="h6 blog-entry-title"><a href="{{ route('catalog.route.recepti', ['cat' => $recepti]) }}">{{ $recepti->translation->title }}</a></h2>
                                <p class="fs-sm">{{ $recepti->translation->short_description }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        @if(isset($faqs) and !$faqs->isEmpty())
            <section class="col">
                <div class="card p2-5 border-0 mt-5 shadow mb-5" >
                    <div class="card-body py-md-4 py-3 px-4 ">

                        <h3> {{ __('front/cart.faq') }}</h3>

                        <div class="rounded-3 p-2 mt-3" style="border: 1px solid rgb(218, 225, 231); background-color: rgb(255, 255, 255) !important;">
                            <div class="accordion accordion-flush" id="accordionFlushExample">
                                @foreach ($faqs as $fa)
                                    <!-- Item -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-heading{{ $fa->id }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{ $fa->id }}" aria-expanded="false" aria-controls="flush-collapse{{ $fa->id }}">{{ $fa->title }}</button>
                                        </h2>
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
        @push('js_after')
            @if (isset($faqs_crumbs) && ! empty($faqs_crumbs))
                <script type="application/ld+json">
                    {!! collect($faqs_crumbs)->toJson() !!}
                </script>
            @endif
        @endpush

    @else
        <div class="mt-2 mb-5 fs-md" style="max-width:1240px">
            <div class=" row pb-2">
                <div class="col-sm-12 mb-2"><img src="{{ asset($recepti->image) }}" alt="{{ $recepti->translation->title }}"></div>
            </div>
            <!-- Post content-->
            {!! $recepti->description !!}
        </div>

        @push('js_after')
            @if (isset($recepti->schema) && ! empty($recepti->schema))
                <script type="application/ld+json">
                    {!! collect($recepti->schema)->toJson() !!}
                </script>
            @endif
        @endpush
    @endif

@endsection
