<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Front\AgCart;
use App\Models\Front\Catalog\Product; // ⬅️ ovaj model
use App\Models\TagManager;            // ⬅️ GA4 payloadi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected $user;
    protected $cart;
    protected $key = 'cart_key';

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->key = config('session.cart');

            if (session()->has($this->key)) {
                $this->cart = new AgCart(session($this->key));
                Cart::checkLogged($this->cart, session($this->key));
            } else {
                $this->resolveSession();
            }

            return $next($request);
        });
    }

    public function get()
    {
        $response = $this->cart->get();
        $this->resolveDB($response);

        return response()->json($response);
    }

    public function check(Request $request)
    {
        $response = $this->cart->check($request);
        $this->resolveDB($response);

        return response()->json($response);
    }

    public function add(Request $request)
    {
        $response = $this->cart->add($request);
        $this->resolveDB($response);

        try {
            $productId = (int) data_get($request->input('item'), 'id');
            $qty       = (int) data_get($request->input('item'), 'quantity', 1);
            if ($productId > 0 && $qty > 0) {
                if ($product = Product::find($productId)) {
                    $response['dl'] = TagManager::addToCart($product, $qty);
                }
            }
        } catch (\Throwable $e) {}

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $response = $this->cart->add($request, $id);
        $this->resolveDB($response);

        try {
            $relative = (bool) data_get($request->input('item'), 'relative', false);
            $qty      = (int) data_get($request->input('item'), 'quantity', 0);
            if ($relative && $qty > 0) {
                if ($product = Product::find((int)$id)) {
                    $response['dl_add'] = TagManager::addToCart($product, $qty);
                }
            }
        } catch (\Throwable $e) {}

        return response()->json($response);
    }

    public function remove($id)
    {
        $response = $this->cart->remove($id);
        $this->resolveDB($response);

        try {
            if (method_exists(TagManager::class, 'removeFromCart')) {
                if ($product = Product::find((int)$id)) {
                    $response['dl_remove'] = TagManager::removeFromCart($product, 1);
                }
            }
        } catch (\Throwable $e) {}

        return response()->json($response);
    }

    public function coupon($coupon)
    {
        session([$this->key . '_coupon' => $coupon]);
        return response()->json($this->cart->coupon($coupon));
    }

    public function loyalty($loyalty)
    {
        session([$this->key . '_loyalty' => $loyalty]);
        return response()->json($this->cart->hasLoyalty());
    }

    private function resolveSession(): void
    {
        $sl_cart_id = Str::random(8);
        $this->cart = new AgCart($sl_cart_id);
        session([$this->key => $sl_cart_id]);

        Cart::checkLogged($this->cart, $sl_cart_id);
    }

    private function resolveDB($response): void
    {
        if (Auth::user()) {
            dispatch(function () use ($response) {
                $has_cart = Cart::where('user_id', Auth::user()->id)->first();
                if ($has_cart) {
                    Cart::edit($response);
                } else {
                    Cart::store($response);
                }
            });
        }
    }
}
