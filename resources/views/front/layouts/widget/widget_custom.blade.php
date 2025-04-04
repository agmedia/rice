<!-- {"title": "Slider Index", "description": "Index main slider."} -->

<section class="tns-carousel mb-3 ">
    <div class="tns-carousel-inner" data-carousel-options="{&quot;items&quot;: 1, &quot;mode&quot;: &quot;gallery&quot;, &quot;nav&quot;: true, &quot;responsive&quot;: {&quot;0&quot;: {&quot;nav&quot;: true, &quot;controls&quot;: true}, &quot;576&quot;: {&quot;nav&quot;: false, &quot;controls&quot;: true}}}">
        @foreach($data as  $widget)
            <div>
                <div class="rounded-3 px-md-5 text-center text-xl-start " style="background: url({{ asset('image/china.jpg') }}) repeat center center fixed; background-size: contain;  ">
                    <div class="d-xl-flex justify-content-between align-items-center px-5  mx-auto" style="max-width: 1226px;">
                        <div class="py-2 py-sm-3 pb-0 me-xl-4 mx-auto mx-xl-0" style="max-width: 490px;">
                            <p class="text-black fs-sm pb-0 mb-1 mt-2 "><i class="ci-bookmark  fs-sm mt-n1 me-2"></i> {{ __('front/ricekakis.top_ponuda') }}</p>
                            <h2 class="h2 text-black font-title mb-1">{{ $widget['title'] }} </h2>
                            <div class="star-rating mb-3"><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i>
                            </div>
                            <p class="text-black pb-1">{{ $widget['subtitle'] }}</p>
                            <div class="d-flex flex-wrap justify-content-center justify-content-xl-start"><a class="btn btn-primary btn-shadow me-2 mb-2" href="{{ url($widget['url']) }}" role="button">{{ __('front/ricekakis.pogledajte_ponudu') }} <i class="ci-arrow-right ms-2 me-n1"></i></a></div>
                        </div>
                        <div><a href="{{ url($widget['url']) }}"><img src="{{ $widget['image'] }}" loading="lazy" alt="{{ $widget['title'] }}" width="400" height="400"></a></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
<!-- How it works-->
