<!-- {"title": "Banneri", "description": "Widget za bannere"} -->
<section class=" py-3 " >

    <div class="row  mt-2 mt-lg-5 ">
        @foreach ($data as $widget)
            <div class="col-lg-12 col-xl-{{ $widget['width'] }} mb-grid-gutter">
                <div class="d-block d-sm-flex justify-content-between align-items-center  rounded-3" style="background: url({{ asset('image/china.jpg') }}) repeat center center fixed; background-size: contain;  ">
                        <div class="pt-5 py-sm-4 px-4 ps-md-4 pe-md-3 text-center text-sm-start" >
                                <h2 class="font-title">{{ $widget['title'] }}</h2>
                                <p class="text-muted pb-2">{!! $widget['subtitle'] !!} </p><a class="btn btn-primary" href="{{ url($widget['url']) }}">Pogledajte ponudu <i class="ci-arrow-right ms-2 me-n1"></i></a>
                        </div>
                       <img class="d-block mx-auto mx-sm-0 rounded-end" src="{{ $widget['image'] }}" width="250" height="250" alt="{{ $widget['title'] }}">
                </div>
            </div>
        @endforeach
    </div>
</section>
