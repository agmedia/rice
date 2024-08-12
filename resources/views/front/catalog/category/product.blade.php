<div class="article pb-1" >

    <div class="card product-card d-flex align-items-stretch  pb-1">


        <div class="btn-wishlist-block">
            @if ($product->vegan)
                <button class="btn-wishlist me-1 " type="button" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Vegan" data-bs-original-title="Vegan"><img src="image/vegan.svg" alt="Vegan" width="15px"/></button>
            @endif
            @if ($product->vegetarian)
                <button class="btn-wishlist me-1 "  type="button" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Vegeterian" data-bs-original-title="Vegeterian"><img src="image/vegeterian.svg" alt="Vegeterian"  width="25px"/></button>
            @endif
            @if ($product->glutenfree)
                <button class="btn-wishlist  me-1"  type="button" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Gluten Free" data-bs-original-title="Gluten Free"><img src="image/gluten-free.svg" alt="Gluten Free"  width="35px"/></button>
            @endif
        </div>


        @if ($product->main_price > $product->main_special or $product->action and !$product->action->min_cart)
            {{ \Illuminate\Support\Facades\Log::info($product->toArray()) }}
            <span class="badge bg-primary badge-shadow">-{{ number_format(floatval(\App\Helpers\Helper::calculateDiscount($product->price, $product->special()))) }}%</span>
        @endif
        <a class="card-img-top pb-2 d-block overflow-hidden" href="{{ url($product->url) }}">
            <img load="lazy" src="{{ str_replace('.webp','-thumb.webp', $product->image) }}" width="400" height="400" alt="{{ $product->name }}">
        </a>
        <div class="card-body pt-2" style="min-height: 120px;">

            <h3 class="product-title fs-sm text-truncate"><a href="{{ url($product->url) }}">{{ $product->name }}</a></h3>
            {!! $product->category_string !!}
            @if ($product->main_price > $product->main_special  or $product->action and !$product->action->min_cart)
                <div class="product-price"><small><span class="text-muted">{{ __('front/ricekakis.nc_30') }}: {{ $product->main_price_text }}  @if($product->secondary_price_text){{ $product->secondary_price_text }} @endif</span></small></div>
                <div class="product-price text-red"><span class="text-red fs-md">{{ $product->main_special_text }} @if($product->secondary_special_text) <small class="text-muted">{{ $product->secondary_special_text }}</small> @endif</span></div>
            @else
                <div class="product-price"><span class="text-dark fs-md">{{ $product->main_price_text }}  @if($product->secondary_price_text) <small class="fs-sm text-muted">{{ $product->secondary_price_text }} </small>@endif</span></div>
            @endif
            @if($product->reviews->count() > 0)
                <div class="star-rating">
                    @for ($i = 0; $i < 5; $i++)
                        @if (floor($product->reviews->avg('stars')) - $i >= 1)
                            {{--Full Start--}}
                            <i class="star-rating-icon ci-star-filled active"></i>
                        @elseif ($product->reviews->avg('stars') - $i > 0)
                            {{--Half Start--}}
                            <i class="star-rating-icon ci-star-half active"></i>
                        @else
                            {{--Empty Start--}}
                            <i class="star-rating-icon ci-star"></i>
                        @endif
                    @endfor
                </div>
            @endif
        </div>
        <div class="product-floating-btn">

            @if ( $product->combo == 0)
            <add-to-cart-btn-simple id="{{ $product->id }}" available="{{ $product->quantity }}"></add-to-cart-btn-simple>
            @else
                <a href="{{ url($product->url) }}" class="btn btn-primary btn-shadow btn-sm"  type="button">+<i class="ci-cart fs-base ms-1"></i></a>


            @endif
        </div>
    </div>
</div>

