<!-- {"title": "Page Carousel", "description": "Category, Publisher, Reviews."} -->
<section class=" py-3 " >
    <div class="d-flex flex-wrap justify-content-between align-items-center pt-1  pb-3 mb-3">
        <h3 class="h3 mb-0 pt-3 font-title me-3"> {{ $data['title'] }}</h3>
    </div>


    @if ($data['tablename'] == 'category')
        <div class="tns-carousel">
            <div class="tns-carousel-inner" data-carousel-options='{"items": 2, "controls": true, "autoHeight": false, "responsive": {"0":{"items":2, "gutter": 10},"480":{"items":2, "gutter": 10},"800":{"items":3, "gutter": 20}, "1300":{"items":4, "gutter": 30}, "1800":{"items":5, "gutter": 30}}}'>
                @foreach ($data['items'] as $item)


                    <!-- Product-->
                    <div class="article mb-grid-gutter">
                        <a class="card border-0 shadow" href="{{ current_locale() }}/{{ $item['group'] }}/{{ $item->translation->slug }}">
                            <img class="card-img-top p-3" loading="lazy" width="400" height="400" src="{{ $item['image'] }}" alt="{{ $item->translation->image_alt }}">
                            <div class="card-body py-2 text-center px-0">
                                <h3 class="h4 mt-1 font-title text-primary">{{ $item->translation->title }}</h3>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

    @elseif ($data['tablename'] == 'brand')
        <div class="tns-carousel">
            <div class="tns-carousel-inner" data-carousel-options='{"items": 2, "controls": true, "autoHeight": false, "responsive": {"0":{"items":2, "gutter": 10},"480":{"items":2, "gutter": 10},"800":{"items":3, "gutter": 20}, "1300":{"items":4, "gutter": 30}, "1800":{"items":5, "gutter": 30}}}'>
                @foreach ($data['items'] as $item)


                    <div class="col-md-3 col-sm-4 col-6"><a class="d-block bg-white shadow-sm rounded-3 py-3 py-sm-4 mb-grid-gutter" href="{{ current_locale() }}/{{ $data['tablename'] }}/{{ $item->translation->slug }}" aria-label="Svi artikli brenda {{ $item->translation->title }}"><img loading="lazy" class="d-block mx-auto" src="{{ $item['image'] }}" style="width: 200px;" alt="Brand {{ $item->translation->title }}"></a></div>
                @endforeach
            </div>
        </div>
    @elseif ($data['tablename'] == 'reviews')

        <div class="tns-carousel">
            <div class="tns-carousel-inner" data-carousel-options='{"items": 1, "controls": false, "autoplay": true, "autoHeight": true, "responsive": {"0":{"items":1, "gutter": 20},"480":{"items":2, "gutter": 20},"800":{"items":3, "gutter": 20}, "1300":{"items":4, "gutter": 30}}}'>
                @foreach ($data['items'] as $review)

                    <blockquote class="mb-2">
                        <div class="card card-body fs-md text-muted border-0 shadow-sm">
                            <div class="mb-2">
                                <div class="star-rating"> @for ($i = 0; $i < 5; $i++)
                                        @if (floor($review->stars) - $i >= 1)
                                            {{--Full Start--}}
                                            <i class="star-rating-icon ci-star-filled active"></i>
                                        @elseif ($review->stars - $i > 0)
                                            {{--Half Start--}}
                                            <i class="star-rating-icon ci-star"></i>
                                        @else
                                            {{--Empty Start--}}
                                            <i class="star-rating-icon ci-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>{{ strip_tags($review->message) }}
                        </div>
                        <footer class="d-flex justify-content-center align-items-center pt-4">
                            <div class="ps-3">
                                <p class="fs-sm fw-bold text-default mb-n1">{{ $review->fname }} {{ $review->lname }}</p>
                            </div>
                        </footer>
                    </blockquote>



                @endforeach
            </div>
        </div>

    @elseif ($data['tablename'] == 'recepti')



        <div class="tns-carousel pb-5">
            <div class="tns-carousel-inner" data-carousel-options="{&quot;items&quot;: 2, &quot;gutter&quot;: 15, &quot;controls&quot;: false, &quot;nav&quot;: true, &quot;responsive&quot;: {&quot;0&quot;:{&quot;items&quot;:1},&quot;500&quot;:{&quot;items&quot;:2},&quot;768&quot;:{&quot;items&quot;:2}, &quot;992&quot;:{&quot;items&quot;:3, &quot;gutter&quot;: 30},&quot;1500&quot;:{&quot;items&quot;:4, &quot;gutter&quot;: 30}}}">
                @foreach ($data['items'] as $item)
                    <!-- Product-->
                    <div>
                        <div class="card">

                            <a class="blog-entry-thumb" href="{{ route('catalog.route.recepti', ['cat' => $item]) }}"><span class="blog-entry-meta-label fs-sm"><i class="ci-pot"></i></span><img class="card-img-top" loading="lazy" src="{!! str_replace('.webp', '-thumb.webp', $item['image']) !!}" width="400" height="230" alt="{{ $item->translation->image_alt }}"></a>

                            <div class="card-body">
                                <h3 class="h6 blog-entry-title"><a href="{{ route('catalog.route.recepti', ['cat' => $item]) }}">{{ $item['title'] }}</a></h3>
                                <p class="fs-sm">{{ $item['short_description'] }}</p>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @else
        <div class="tns-carousel pb-5">
            <div class="tns-carousel-inner" data-carousel-options="{&quot;items&quot;: 2, &quot;gutter&quot;: 15, &quot;controls&quot;: false, &quot;nav&quot;: true, &quot;responsive&quot;: {&quot;0&quot;:{&quot;items&quot;:1},&quot;500&quot;:{&quot;items&quot;:2},&quot;768&quot;:{&quot;items&quot;:2}, &quot;992&quot;:{&quot;items&quot;:3, &quot;gutter&quot;: 30}}}">
                @foreach ($data['items'] as $item)
                    <!-- Product-->
                    <div>
                        <div class="card"><a class="blog-entry-thumb" href="{{ route('catalog.route.blog', ['cat' => $item]) }}"><img class="card-img-top" loading="lazy" src="{{ $item['image'] }}" width="400" height="230" alt="{{ $item->translation->image_alt }}"></a>
                            <div class="card-body">
                                <h3 class="h6 blog-entry-title"><a href="{{ route('catalog.route.blog', ['cat' => $item]) }}">{{ $item['title'] }}</a></h3>
                                <p class="fs-sm">{{ $item->translation['short_description'] }}</p>
                                <div class="fs-xs text-nowrap"><a class="blog-entry-meta-link text-nowrap" href="#">{{ \Carbon\Carbon::make($item['created_at'])->locale('hr')->format('d.m.Y.') }}</a></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @endif



</section>
