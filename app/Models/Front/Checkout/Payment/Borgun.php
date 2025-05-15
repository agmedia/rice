<?php

namespace App\Models\Front\Checkout\Payment;

use App\Models\Back\Orders\Order;
use App\Models\Back\Orders\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class Payway
 * @package App\Models\Front\Checkout\Payment
 */
class Borgun
{

    /**
     * @var Order
     */
    private $order;

    /**
     * @var string[]
     */
    private $url = [
        'test' => 'https://test.borgun.is/SecurePay/default.aspx',
        'live' => 'https://securepay.borgun.is/securepay/default.aspx'
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

        $total = number_format($this->order->total,2, '.', '');

        $merchantid         = '9256684';
        $secretkey          = 'cdedfbb6ecab4a4994ac880144dd92dc';
        $payment_gateway_id = '7';
        $success_url        = $payment_method->data->callback;
        $orderid = $this->order->id;
        $currency_code = 'EUR';

        $data['order_id'] = $orderid;

        $data['products'] = array();

        foreach ( $this->order->totals->toArray() as $product ) {
            if($product['code'] =='shipping'   ) {
                $data['products'][] = array(
                    'name'     => $product['title'],
                    'price' =>  $product['value'],
                    'quantity' => 1,
                    'total' =>  $product['value'],
                );
            }

            if( $product['code'] =='special' and $product['value'] > 0 ) {
                $data['products'][] = array(
                    'name'     => $product['title'],
                    'price' => - $product['value'],
                    'quantity' => 1,
                    'total' => - $product['value'],
                );
            }
        }

         $data['products'] = array_merge($this->order->products->toArray(),$data['products']);


       // dd($data['products']);


        $grand_total         = $total;

        if($grand_total < 0) $grand_total = 0;
        $data['grand_total'] = $grand_total;

        $CheckHashMessage = $merchantid . '|' . $success_url . '|' . $success_url . '|' . $orderid . '|' . $grand_total . '|' . $currency_code;

        $CheckHashMessage = utf8_encode( trim( $CheckHashMessage ) );
        $checkhash        = hash_hmac( 'sha256', $CheckHashMessage, $secretkey );

        $data['checkhash'] = $checkhash;

        $data['merchantid']       = $merchantid;
        $data['paymentgatewayid'] = $payment_gateway_id;
        $data['currency'] = 'EUR';
        $data['action'] = $action;
        $data['language'] = 'HR';

        $data['shop_id'] = $payment_method->data->shop_id;
        $data['total'] = $total;

        $data['firstname'] = $this->order->payment_fname;
        $data['lastname'] = $this->order->payment_lname;
        $data['address'] = $this->order->payment_address;
        $data['city'] = $this->order->payment_city;
        $data['country'] = $this->order->payment_state;
        $data['postcode'] = $this->order->payment_zip;
        $data['phone'] = $this->order->payment_phone;
        $data['email'] = $this->order->payment_email;

        $data['return'] = $payment_method->data->callback;
        $data['cancel'] = route('kosarica');

        return view('front.checkout.payment.borgun', compact('data'));
    }


    /**
     * @param Order $order
     * @param null  $request
     *
     * @return bool
     */
    public function finishOrder(Order $order, Request $request): bool
    {

         $status = ($request->has('status') && $request->input('status') == 'OK') ? config('settings.order.status.paid') : config('settings.order.status.declined');;

        $order->update([
            'order_status_id' => $status
        ]);

        Transaction::insert([
            'order_id' => $request->input('orderid'),
            'success' => 0,
            'amount' => $order->total,
            'signature' => $request->input('orderhash'),
            'payment_type' => 'borgun',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);


        if ($request->input('status') == 'OK') {
            Transaction::query()->where('order_id', $order->id)->update([
                'success' => 1,
                'error' => json_encode($request->toArray()),
            ]);

            return true;
        }

        Transaction::query()->where('order_id', $order->id)->update([
            'success' => 0,
            'error' => $request->input('ErrorMessage'),
        ]);

        return false;
    }





}
