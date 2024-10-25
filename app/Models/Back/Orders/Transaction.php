<?php

namespace App\Models\Back\Orders;

use App\Models\Front\Checkout\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    //
    /**
     * @var string
     */
    protected $table = 'order_transactions';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];


    public static function checkStatus(int $order_id = null)
    {
        $payment_code = config('settings.order.check_statuses_payment');
        $orders = Order::query()->where('payment_code', $payment_code);

        if ($order_id) {
            $orders = $orders->where('id', $order_id)->get();
        } else {
            $orders = $orders->whereIn('status', config('settings.order.check_statuses'))
                                    ->where('created_at', '>', now()->subDays(config('settings.order.check_statuses_days')))
                                    ->get();
        }

        Log::info('Transaction check status', ['order_id' => $order_id]);
        Log::info($orders->count());

        if ( ! $orders->isEmpty()) {
            Log::info('Order check status', ['order_id' => $order_id]);
            $payment_method = new PaymentMethod($payment_code);
            $payment_provider = $payment_method->getProvider($payment_code);

            foreach ($orders as $order) {
                $payment = new $payment_provider($order);

                $payment->checkStatus($payment_method->getMethod()->first());
            }
        }

        return $orders->count();
    }
}
