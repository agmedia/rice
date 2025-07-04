<!-- {"title": "Carousel", "description": "Widget za product carousel"} -->
<section class="pt-2">

    <div class="d-flex flex-wrap justify-content-between align-items-center pt-1  pb-2 mb-2">
        <h3 class="h3 mb-0 pt-3 font-title me-3">{{ $data['title'] }}</h3>
        @if($data['subtitle'])  <p class="text-muted text-center mb-5">{{ $data['subtitle'] }}</p> @endif
        @if($data['url'] !='/')
            <a class="btn btn-outline-primary btn-sm btn-shadow mt-3" aria-label="View Offer" href="{{ url($data['url']) }}"><span class="d-none d-sm-inline-block">{{ __('front/ricekakis.pogledajte_sve') }}</span> <i class="ci-arrow-right "></i></a>
        @endif

    </div>
    <div class="tns-carousel pt-4 pb-2">
        <div class="tns-carousel-inner" data-carousel-options='{"items": 2, "controls": true, "nav": true, "autoHeight": true, "responsive": {"0":{"items":2, "gutter": 10},"500":{"items":2, "gutter": 10},"768":{"items":3, "gutter": 10}, "1100":{"items":4, "gutter": 15}, "1500":{"items":5, "gutter": 20}}}'>
            @foreach ($data['items'] as $product)
                <!-- Product-->
                <div>
                    @include('front.catalog.category.product')
                </div>
            @endforeach
        </div>
    </div>


</section>
