<?php

namespace App\Models\Front\Checkout\Payment;

use App\Models\Back\Orders\Order;
use App\Models\Back\Orders\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class Payway
 * @package App\Models\Front\Checkout\Payment
 */
class WSpay
{

    /**
     * @var Order
     */
    private $order;

    /**
     * @var string[]
     */
    private $url = [
        'test' => 'https://formtest.wspay.biz/Authorization.aspx',
        'live' => 'https://form.wspay.biz/Authorization.aspx'
    ];


    private $check_url = [
        'test' => 'https://test.wspay.biz/api/services/statusCheck',
        'live' => 'https://secure.wspay.biz/api/services/statusCheck'
    ];


    /**
     * Payway constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }


    /**
     * @param Collection|null $payment_method
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function resolveFormView(Collection $payment_method = null)
    {
        if ( ! $payment_method) {
            return '';
        }

        $payment_method = $payment_method->first();

        $action = $this->url['live'];

        if ($payment_method->data->test) {
            $action = $this->url['test'];
        }

        $total = number_format($this->order->total,2, ',', '');
        $_total = str_replace( ',', '', $total);

        $shoppingcartid = $this->order->id;

        // $stringhash = $payment_method->data->shop_id.$payment_method->data->secret_key.$shoppingcartid.$payment_method->data->secret_key.$_total.$payment_method->data->secret_key;



        // $hash = hash('sha512', $stringhash);

        $hash = md5($payment_method->data->shop_id .
            $payment_method->data->secret_key .
            $this->order->id.'-'.date("Y") .
            $payment_method->data->secret_key .
            $_total.
            $payment_method->data->secret_key
        );

        $data['action'] = $action;
        $data['shop_id'] = $payment_method->data->shop_id;
        $data['order_id'] = $this->order->id.'-'.date("Y");
        $data['total'] = $total;
        $data['md5'] = $hash;
        $data['firstname'] = $this->order->payment_fname;
        $data['lastname'] = $this->order->payment_lname;
        $data['address'] = $this->order->payment_address;
        $data['city'] = $this->order->payment_city;
        $data['country'] = $this->order->payment_state;
        $data['postcode'] = $this->order->payment_zip;
        $data['phone'] = $this->order->payment_phone;
        $data['email'] = $this->order->payment_email;
        $data['lang'] = 'HR';
        $data['plan'] = '';
        $data['cc_name'] = '';//...??
        $data['currency'] = 'EUR';
        $data['rate'] = 1;
        $data['return'] = $payment_method->data->callback;
        $data['cancel'] = route('kosarica');
        $data['method'] = 'POST';

        return view('front.checkout.payment.wspay', compact('data'));
    }


    /**
     * @param Order $order
     * @param null  $request
     *
     * @return bool
     */
    public function finishOrder(Order $order, Request $request): bool
    {
        $status = $request->input('Success') ? config('settings.order.status.paid') : config('settings.order.status.declined');

        $order->update([
            'order_status_id' => $status
        ]);

        Transaction::insert([
            'order_id' => $order->id,
            //'success' => 1,
            'amount' => $request->input('Amount'),
            'signature' => $request->input('Signature'),
            'payment_type' => 'wspay',
            'payment_plan' => $request->input('PaymentPlan'),
            'payment_partner' => $request->input('Partner'),
            'datetime' => $request->input('"TransactionDateTime'),
            'approval_code' => $request->input('"ShoppingCartID'),
            'pg_order_id' => $request->input('"WsPayOrderId'),
            'lang' => 'HR',
            'stan' => $request->input('STAN'),
            //'error' => $request->input('ErrorMessage'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);


        if ($request->input('Success')) {
            Transaction::query()->where('order_id', $order->id)->update([
                'success' => 1,
                'error' => $request->json(),
            ]);

            return true;
        }

        Transaction::query()->where('order_id', $order->id)->update([
            'success' => 0,
            'error' => $request->input('ErrorMessage'),
        ]);

        return false;
    }


    public function checkStatus(\stdClass $payment_method, Order $order = null)
    {
        if ( ! $order && ! $this->order) {
            return false;
        }

        if ($order) {
            $this->order = $order;
        }

        if ($this->order->transaction()->count()) {
            $action = $this->check_url['live'];

            if ($payment_method->data->test) {
                $action = $this->check_url['test'];
            }

            $secret_key = $payment_method->data->secret_key;
            $shop_id = $payment_method->data->shop_id;
            $shop_cart_id = $this->order->transaction->approval_code;
            $signature = md5($shop_id . $secret_key . $shop_cart_id . $secret_key . $shop_id . $shop_cart_id);

            $post_data = [
                'Version' => '2.0',
                'ShopID' => $shop_id,
                'ShoppingCartID' => $shop_cart_id,
                'Signature' => $signature
            ];

            try {
                $response = Http::post($action, $post_data);

                Log::info('json_encode($response)');
                Log::info(json_encode($response));

            } catch (\Exception $e) {
                Log::error($e->getMessage());

                return false;
            }
        }

        return true;
    }

}
