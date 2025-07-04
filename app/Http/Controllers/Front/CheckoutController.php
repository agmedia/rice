<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Session\CheckoutSession;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontBaseController;
use App\Mail\OrderReceived;
use App\Mail\OrderSent;
use App\Models\Back\Settings\Settings;
use App\Models\Front\AgCart;
use App\Models\Front\Checkout\Order;
use App\Models\Front\Checkout\Shipping\Gls;
use App\Models\Front\Loyalty;
use App\Models\TagManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use SoapClient;
use \stdClass;

class CheckoutController extends FrontBaseController
{

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function cart(Request $request)
    {
        if ($this->comboSessionProblem()) {
            //Log::info('Tu sam 3.1');
            return redirect($this->getComboUrl())->with(['error' => 'Molimo odaberite combo proizvod.']);
        }

        $gdl = TagManager::getGoogleCartDataLayer($this->shoppingCart()->get());

        return view('front.checkout.cart', compact('gdl'));
    }


    /**
     * @param Request $request
     * @param string  $step
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function checkout(Request $request)
    {
        $step = '';

        if ($request->has('step')) {
            $step = $request->input('step');
        }

        $is_free_shipping = (config('settings.free_shipping') < $this->shoppingCart()->get()['total']) ? true : false;

        return view('front.checkout.checkout', compact('step', 'is_free_shipping'));
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view(Request $request)
    {
        if ($this->comboSessionProblem()) {
            return redirect($this->getComboUrl())->with(['error' => 'Molimo odaberite combo proizvod.']);
        }

        $data = $this->checkSession();

        if (empty($data)) {
            if ( ! session()->has(config('session.cart'))) {
                return redirect()->route('kosarica');
            }

            return redirect()->route('naplata', ['step' => 'podaci']);
        }

        $data = $this->collectData($data, config('settings.order.status.unfinished'));

        $order = new Order();

        if (CheckoutSession::hasOrder()) {
            $data['id'] = CheckoutSession::getOrder()['id'];

            $order->updateData($data);
            $order->setData($data['id']);

        } else {
            $order->createFrom($data);
        }

        if ($order->isCreated()) {
            CheckoutSession::setOrder($order->getData());
        }

        if ( ! isset($data['id'])) {
            $data['id'] = CheckoutSession::getOrder()['id'];
        }

        $uvjeti = null;//DB::table('pages')->select('description')->whereIn('id', [6])->get();

        $data['payment_form'] = $order->resolvePaymentForm();

        return view('front.checkout.view', compact('data', 'uvjeti'));
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function order(Request $request)
    {
        $order = new Order();

        Log::info('Response Corvus::::::::::::::::::::::::::::::::::::::');
        Log::info($request->toArray());

        if ($request->has('provjera')) {
            $order->setData($request->input('provjera'));
        }

        if ($request->has('order_number')) {
            $order->setData($request->input('order_number'));
        }

        if ($request->has('ShoppingCartID')) {
            $id = str_replace('-' . now()->format('Y'), '', $request->input('ShoppingCartID'));

            $order->setData($id);
        }

        if ($request->has('orderid')) {
            $id =  $request->input('orderid');

            $order->setData($id);
        }

        if ($order->finish($request)) {
            if ($request->has('return_json') && intval($request->input('return_json'))) {
                return response()->json(['success' => 1, 'href' => route('checkout.success')]);
            }

            return redirect()->route('checkout.success');
        }

        return redirect()->route('checkout.error');
    }

    public function orderBorgun(Request $request)
    {
        $order = new Order();

        Log::info('Response Corvus::::::::::::::::::::::::::::::::::::::');
        Log::info($request->toArray());


        if ($request->has('orderid')) {
            $id =  $request->input('orderid');

            $order->setData($id);
        }

        if ($order->finish($request)) {
            if ($request->has('return_json') && intval($request->input('return_json'))) {
                return response()->json(['success' => 1, 'href' => route('checkout.success')]);
            }

            return redirect()->route('checkout.success', ['oid' => $id]);
        }

        return redirect()->route('checkout.error');
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function success(Request $request)
    {
        Log::info($request->toArray());

        $data['order'] = CheckoutSession::getOrder();

        if ( ! $data['order']) {
            if ($request->has('oid')) {
                $data['order']['id'] = $request->input('oid');

            } else {
                return redirect()->route('index');
            }
        }

        $order = \App\Models\Back\Orders\Order::where('id', $data['order']['id'])->first();

        if ($request->has('oid')) {
            $data['order'] = $order;
        }

        if ($order) {
            $cart = $this->shoppingCart();

            //Log::info($cart->get());

            $order->decreaseItems($order->products);
            //Log::info($order);
            //Log::info($order['payment_code']);
            if($order['payment_code'] !='cod'){
            Loyalty::resolveOrder($cart->get(), $order);
            }
            dispatch(function () use ($order) {
                Mail::to(config('mail.admin'))->send(new OrderReceived($order));
                Mail::to($order->payment_email)->send(new OrderSent($order));
            })->afterResponse();


            // Sent labels to gls
          //  $gls   = new Gls($order);
           // $label = $gls->resolve();

            $this->forgetCheckoutCache();

            $cart->flush()->resolveDB();

            $data['google_tag_manager'] = TagManager::getGoogleSuccessDataLayer($order);

            return view('front.checkout.success', compact('data'));
        }

        return redirect()->route('front.checkout.checkout', ['step' => '']);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successKeks(Request $request)
    {
        if ($this->validateKeksResponse($request)) {
            $id    = substr($request->input('bill_id'), 18);
            $order = Order::query()->where('id', $id)->first();

            $order->setData($id)->finish($request);

            $order->update([
                'order_status_id' => config('settings.order.new_status')
            ]);

            $this->forgetCheckoutCache();

            return response()->json(['status' => 0, 'message' => 'Accepted']);
        }

        return response()->json(['status' => 1, 'message' => 'Failed']);
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function error()
    {
        return view('front.checkout.error');
    }


    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @return array
     */
    private function checkSession(): array
    {
        if (CheckoutSession::hasAddress() && CheckoutSession::hasShipping() && CheckoutSession::hasPayment()) {
            return [
                'address'  => CheckoutSession::getAddress(),
                'shipping' => CheckoutSession::getShipping(),
                'payment'  => CheckoutSession::getPayment(),
                'comment'  => CheckoutSession::getComment()
            ];
        }

        return [];
    }


    /**
     * @param array $data
     * @param int   $order_status_id
     *
     * @return array
     */
    private function collectData(array $data, int $order_status_id): array
    {
        $shipping = Settings::getList('shipping')->where('code', $data['shipping'])->first();
        $payment  = Settings::getList('payment')->where('code', $data['payment'])->first();

        $response                    = [];
        $response['address']         = $data['address'];
        $response['shipping']        = $shipping;
        $response['payment']         = $payment;
        $response['comment']         = isset($data['comment']) ? $data['comment'] : '';
        $response['cart']            = $this->shoppingCart()->get();
        $response['order_status_id'] = $order_status_id;

        return $response;
    }


    /**
     * @param Request $request
     *
     * @return bool
     */
    private function validateKeksResponse(Request $request): bool
    {
        if ($request->has('status') && ! $request->input('status')) {
            $token = $request->header('Authorization');

            if ($token) {
                $keks_token = Settings::get('payment', 'list.keks')->first();

                if (isset($keks_token->data->token)) {
                    return hash_equals($keks_token->data->token, str_replace('Token ', '', $token));
                }
            }
        }

        return false;
    }


    /**
     * @return AgCart
     */
    private function shoppingCart(): AgCart
    {
        if (session()->has(config('session.cart'))) {
            return new AgCart(session(config('session.cart')));
        }

        return new AgCart(config('session.cart'));
    }


    /**
     * @return bool
     */
    private function comboSessionProblem(): bool
    {
        $combo_session_problem = false;
        $items = $this->shoppingCart()->getCartItems();

        foreach ($items as $item) {
            if ($item->associatedModel->combo && session()->has('combo.' . $item->id)) {
                $key = 'combo.' . $item->id;
                $session = session($key);

                /*Log::info('$session');
                Log::info($session);

                Log::info('$item->associatedModel->combo_set');
                Log::info($item->associatedModel->combo_set);*/

                foreach ($item->associatedModel->combo_set as $combo_id => $combo_item) {
                    /*Log::info('$combo_id');
                    Log::info($combo_id);*/
                    if ( ! isset($session[$combo_id])) {
                        //$combo_session_problem = true;

                        $session[$combo_id] = collect($combo_item['products'])->first()['id'];

                        session([$key => $session]);
                    }
                }
            }
        }

        return $combo_session_problem;
    }


    /**
     * @return string
     */
    private function getComboUrl(): string
    {
        $items = $this->shoppingCart()->getCartItems();

        foreach ($items as $item) {
            if ($item->associatedModel->combo) {
                return $item->associatedModel->url ?? '';
            }
        }

        return route('index');
    }


    /**
     * @return void
     */
    private function forgetCheckoutCache(): void
    {
        CheckoutSession::forgetOrder();
        CheckoutSession::forgetStep();
        CheckoutSession::forgetPayment();
        CheckoutSession::forgetShipping();
        CheckoutSession::forgetComment();
    }

}
