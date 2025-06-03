@extends('front.layouts.app')
@section ( 'title', 'Brandovi - Rice Kakis | Asian Store' )
@section ( 'description', 'Naši brandovi azijskih namirnica' )
@if (isset($meta_tags))
    @push('meta_tags')
        @foreach ($meta_tags as $tag)
            <meta name={{ $tag['name'] }} content={{ $tag['content'] }}>
        @endforeach
        <link rel="alternate" href="https://www.ricekakis.com/brand/" hreflang="hr" />
        <link rel="alternate" href="https://www.ricekakis.com/en/brand/" hreflang="en" />
        <link rel="alternate" href="https://www.ricekakis.com/brand/" hreflang="x-default"/>
    @endpush
@endif
@section('content')
    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb flex-lg-nowrap">
            <li class="breadcrumb-item"><a class="text-nowrap" href="{{ route('index') }}"><i class="ci-home"></i>{{ __('front/ricekakis.homepage') }}</a></li>
            <li class="breadcrumb-item text-nowrap active" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route.brand') }}">{{ __('front/ricekakis.lista_brandova') }}</a></li>
        </ol>
    </nav>
    <section class="d-md-flex justify-content-between align-items-center mb-2 pb-2">
        <h1 class="h2 mb-1 mb-md-0 me-3">{{ __('front/ricekakis.lista_brandova') }}</h1>

    </section>
    <!-- Topics grid-->
    <section class=" py-1 mb-5">
        <div class="row align-items-center py-md-1">
            <div class="col-lg-12   py-2 ">
                <div class="scrolling-wrapper">
                    @foreach ($letters as $item)
                        <a href="{{ route('catalog.route.brand', ['brand' => null, 'letter' => $item['value']]) }}"
                           class="btn btn-outline-primary btn-sm text-white  bg-primary mb-2 @if( ! $item['active'])  disabled @endif @if($item['value'] == $letter) bg-primary  @endif">
                            <strong>{{ $item['value'] }}</strong></a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row py-md-3">
            <div class="col-lg-12  mt-3 mb-2">
                <h2 class="h5">{{ $letter ?: 'Svi brandovi' }}</h2>

            </div>
            @foreach ($brands as $brand)


                <div class="col-sm-6 col-md-4  mb-3">
                    <div class="card border-bottom-primary">
                        <div class="card-body">
                            <h6 class="card-title mb-0"><a href="{{ url($brand->url) }}" class="text-dark">{{ $brand->translation->title }} <span class="badge rounded-pill bg-secondary float-end">{{ $brand->products_count }}</span></a></h6>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row  py-md-3">

            {{ $brands->onEachSide(1)->links() }}

        </div>
    </section>

@endsection

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
