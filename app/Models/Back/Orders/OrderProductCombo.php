<?php

namespace App\Models\Back\Orders;

use App\Models\Back\Catalog\Product\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class OrderProductCombo extends Model
{

    /**
     * @var string
     */
    protected $table = 'order_products_combo';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }


    /**
     * @param $value
     *
     * @return mixed
     */
    public function getSelectedAttribute($value)
    {
        return json_decode($value, true);
    }


    /**
     * @param int $order_id
     * @param int $product_id
     *
     * @return bool
     */
    public static function storeData(int $order_id, int $product_id): bool
    {
        $session_key = 'combo.' . $product_id;

        if (session()->has($session_key)) {
            self::query()->where('order_id', $order_id)
                ->where('product_id', $product_id)
                ->delete();

            $selected_data = [];
            $session       = session($session_key);

            foreach ($session as $combo_id => $selected_product_id) {
                $selected_data[] = [
                    'combo_id'         => $combo_id,
                    'selected_product' => $selected_product_id
                ];
            }

            $saved = self::insertGetId([
                'order_id'   => $order_id,
                'product_id' => $product_id,
                'selected'   => collect($selected_data)->toJson(),
                'created_at' => \Illuminate\Support\Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            if ( ! $saved) {
                return false;
            }
        }

        return true;
    }

}
