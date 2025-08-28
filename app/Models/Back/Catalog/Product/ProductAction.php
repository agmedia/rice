<?php

namespace App\Models\Back\Catalog\Product;


use App\Services\Pricing\PriceWriter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProductAction extends Model
{

    /**
     * @var string
     */
    protected $table = 'product_actions';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    /**
     * @return mixed
     */
    public static function active()
    {
        return self::where('date_start', '<', Carbon::now())
            ->where('date_end', '>', Carbon::now())
            //->orWhereNull('date_start')
            ->orWhereNull('date_end');
    }


    /**
     * @param $request
     * @param $id
     *
     * @return bool
     * @throws \Exception
     */
    public static function store($request)
    {
        if (isset($request->products)) {
            foreach ($request->products as $product) {
                $_product = Product::query()->where('id', $product)->first();

                if ($_product) {
                    // Delete, if any action exist
                    self::where('product_id', $product)->delete();

                    // Insert new action
                    $_id = self::insertGetId([
                        'product_id' => $product,
                        'name'       => $request->name,
                        'coupon'     => $request->code,
                        'price'      => $request->price,
                        'discount'   => $request->discount,
                        'date_start' => $request->date_start ? new Carbon($request->date_start) : null,
                        'date_end'   => $request->date_end ? new Carbon($request->date_end) : null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    app(PriceWriter::class)->setPrice($_product, $request->price);
                }
            }

            return $_id;
        }

        return false;
    }
}
