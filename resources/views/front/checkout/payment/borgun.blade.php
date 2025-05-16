<form name="pay" class="needs-validation w-100" action="{{ $data['action'] }}" method="POST">

    @foreach ($data['products'] as $product )
        <input type="hidden" name="itemdescription_{{ $loop->index }}" value="{{ substr($product['name'], 0,  80)  }}" />
        <input type="hidden" name="itemcount_{{ $loop->index }}" value="{{ $product['quantity'] }}" />
        <input type="hidden" name="itemunitamount_{{ $loop->index }}" value="{{ number_format($product['price'], 2, '.', '') }}" />
        <input type="hidden" name="itemamount_{{ $loop->index }}" value="{{ number_format($product['total'], 2, '.', '') }}" />
    @endforeach

    <input type="hidden" name="merchantid" value="{{ $data['merchantid'] }}"/>

    <input type="hidden" name="amount" value="{{ $data['total'] }}"/>

    <input type="hidden" name="customField" value="{{ $data['total'] }}"/>

    <input type="hidden" name="pagetype" value="0"/>
        <input type="hidden" name="merchantemail" value="ricekakis@gmail.com"/>



    <input type="hidden" name="skipreceiptpage" value="1"/>

    <input type="hidden" name="paymentgatewayid" value="{{ $data['paymentgatewayid'] }}"/>

    <input type="hidden" name="checkhash" value="{{ $data['checkhash'] }}"/>

    <input type="hidden" name="orderid" value="{{ $data['order_id']  }}"/>

    <input type="hidden" name="currency" value="{{ $data['currency']  }}"/>
        <input type="hidden" name="merchantlogo" value="https://www.ricekakis.com/img/logo-kakis.png"/>


    <input type="hidden" name="language" value="{{  $data['language']  }}"/>

    <input type="hidden" name="buyeremail" value="{{ $data['email'] }}"/>

    <input type="hidden" name="merchantemail" value="info@ricekakis.com"/>

    <input type="hidden" name="returnurlsuccess" value="{{ $data['return'] }}"/>

    <input type="hidden" name="returnurlerror" value="{{ $data['cancel'] }}"/>

    <input type="hidden" name="returnurlcancel" value="{{ $data['cancel'] }}"/>

    <input type="hidden" name="returnurlsuccessserver" value="{{ $data['return'] }}"/>




    <div class="form-check form-check-inline">
        <label class="form-check-label" for="ex-check-4">{{ __('front/cart.slazem_se_sa') }} {!! __(' :terms_of_service', [
                                                'terms_of_service' => '<a data-bs-toggle="modal" data-bs-target="#exampleModal" class="link-fx">'.__('front/cart.uvijetima_kupovine').'</a>',
                                                'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="link-fx">'.__('Privacy Policy').'</a>',
                                        ]) !!}</label>
        <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
        <div class="invalid-feedback" id="terms">{{ __('front/cart.morate_se_sloziti') }}</div>
    </div>


    <div class="d-flex mt-3">
        <div class="w-50 pe-3"><a class="btn btn-outline-primary d-block w-100" href="{{ route('naplata') }}"><i class="ci-arrow-left  me-1"></i><span class="d-none d-sm-inline">{{ __('front/cart.povratak_na_placanje') }}</span><span class="d-inline d-sm-none">{{ __('front/cart.povratak') }}</span></a></div>
        <div class="w-50 ps-2"><button class="btn btn-primary d-block w-100" type="submit"><span class="d-none d-sm-inline">{{ __('front/cart.dovrsi_kupnju') }}</span><span class="d-inline d-sm-none">{{ __('front/cart.dovrsi_kupnju') }}</span><i class="ci-arrow-right ms-1"></i></button></div>
    </div>
    <div class="clearfix"></div>
</form>
