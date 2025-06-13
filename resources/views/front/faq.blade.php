@extends('front.layouts.app')

@if(current_locale() == 'hr')
    @section ( 'title', 'Česta pitanja – Dostava, plaćanje i narudžbe | Rice Kakis' )
    @section ( 'description', 'Ovdje saznajte sve o narudžbama, dostavi, plaćanju, loyalty klubu i rješavanju problema s paketom. Odgovori na najčešća pitanja na jednom mjestu.' )
@else
    @section ( 'title', 'FAQ – Delivery, Payment & Orders | Rice Kakis' )
    @section ( 'description', 'Get answers about ordering, delivery options, payment methods, loyalty points, and what to do if your package is delayed, damaged or missing.   ' )
@endif

@section('content')

    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb flex-lg-nowrap">
            <li class="breadcrumb-item"><a class="text-nowrap" href="{{ route('index') }}"><i class="ci-home"></i>{{ __('front/ricekakis.homepage') }}</a></li>
            <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('front/cart.faq') }}</li>
        </ol>
    </nav>

    <section class="d-md-flex justify-content-between align-items-center mb-4 pb-2">
        <h1 class="h2 mb-3 mb-md-0 me-3">{{ __('front/cart.faq') }}</h1>
    </section>

    <div class="mt-5 mb-5" style="max-width:1240px">
        <!-- Flush accordion. Use this when you need to render accordions edge-to-edge with their parent container -->
        <div class="rounded-3 p-4 mt-3" style="border: 1px solid rgb(218, 225, 231); background-color: rgb(255, 255, 255) !important;">
        <div class="accordion accordion-flush" id="accordionFlushExample">
            @foreach ($faq as $fa)
                @if($fa->category_id == 0)
                <!-- Item -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-heading{{ $fa->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{ $fa->id }}" aria-expanded="false" aria-controls="flush-collapse{{ $fa->id }}">{{ $fa->title }}</button>
                    </h2>
                    <div class="accordion-collapse collapse" id="flush-collapse{{ $fa->id }}" aria-labelledby="flush-heading{{ $fa->id }}" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">  {!! $fa->description !!}</div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    </div>

@endsection
