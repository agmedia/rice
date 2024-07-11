@extends('front.layouts.app')

@section('content')




    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb flex-lg-nowrap">
            <li class="breadcrumb-item"><a class="text-nowrap" href="{{ route('index') }}"><i class="ci-home"></i>{{ __('front/ricekakis.homepage') }}</a></li>
            <li class="breadcrumb-item text-nowrap active" aria-current="page">Kontakt</li>
        </ol>
    </nav>


    <section class="d-md-flex justify-content-between align-items-center mb-4 pb-2">
        <h1 class="h2 mb-3 mb-md-0 me-3">Kontakt</h1>

    </section>



    <!-- Contact detail cards-->
    <section class=" pt-grid-gutter">
        <div class="row">

            @include('front.layouts.partials.success-session')

            <div class="col-12 col-sm-6 mb-5">

                        <h3 class=" mb-4">Impressum</h3>
                <p>Vukoje Logistika j.d.o.o., Kaštelanska 4a. Veliko Polje, 10010 Zagreb</p>
                <p>Registarski sud: Trgovački sud u Zagrebu</p>  <p>MBS: 081362286 <br>
                        OIB: 04676029695</p> <p>Osnivači/članovi društva: Luka Vukoje</p> <p>Žiro račun otvoren u: Privredna banka Zagreb d.d.<br> IBAN: HR9223400091111126783<br> SWIFT: PBZGHR2X</p>

            </div>

            <div class="col-12 col-sm-6 mb-5 ">
                <h2 class="h4 mb-4">Pošaljite upit</h2>
                <form action="{{ route('poruka') }}" method="POST" class="mb-3">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label class="form-label" for="cf-name">Vaše ime:&nbsp;@include('back.layouts.partials.required-star')</label>
                            <input class="form-control" type="text" name="name" id="cf-name" placeholder="">
                            @error('name')<div class="text-danger font-size-sm">Molimo upišite vaše ime!</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="cf-email">Email adresa:&nbsp;@include('back.layouts.partials.required-star')</label>
                            <input class="form-control" type="email" id="cf-email" placeholder="" name="email">
                            @error('email')<div class="invalid-feedback">Molimo upišite ispravno email adresu!</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="cf-phone">Broj telefona:&nbsp;@include('back.layouts.partials.required-star')</label>
                            <input class="form-control" type="text" id="cf-phone" placeholder="" name="phone">
                            @error('phone')<div class="invalid-feedback">Molimo upišite broj telefona!</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="cf-message">Upit:&nbsp;@include('back.layouts.partials.required-star')</label>
                            <textarea class="form-control" id="cf-message" rows="6" placeholder="" name="message"></textarea>
                            @error('message')<div class="invalid-feedback">Molimo upišite poruku!</div>@enderror
                            <button class="btn btn-primary mt-4" type="submit">Pošaljite upit</button>
                        </div>
                    </div>
                    <input type="hidden" name="recaptcha" id="recaptcha">
                </form>
            </div>

            <div class="col-md-12"><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d11124.017953831606!2d15.979905!3d45.8111685!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47667fd7306782ed%3A0xa50559d5f9f01e7d!2sRice%20Kakis%20Asian%20Store!5e0!3m2!1shr!2shr!4v1720693241731!5m2!1shr!2shr" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
        </div>
    </section>






@endsection

@push('js_after')
    @include('front.layouts.partials.recaptcha-js')
@endpush
