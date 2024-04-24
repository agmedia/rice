<?php

namespace App\Models\Back\Catalog\Product;

use App\Models\Back\Catalog\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProductCombo extends Model
{

    /**
     * @var string $table
     */
    protected $table = 'product_combo';

    /**
     * @var array $guarded
     */
    protected $guarded = [];


    /**
     * @param $value
     *
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return json_decode($value, true);
    }


    /**
     * @param int     $id
     * @param Request $request
     *
     * @return bool
     */
    public static function storeData(int $id, Request $request): bool
    {
        self::query()->where('product_id', $id)->delete();

        foreach ($request->input('combo_title') as $key => $title) {
            $group    = Str::slug($title['en'] ?? Str::random(9));
            $value    = ['title' => $title];
            $products = $request->input('action_list')[$key] ?? null;

            $saved = self::insertGetId([
                'product_id' => $id,
                'group'      => $group,
                'products'   => collect($products)->flatten()->toJson(),
                'value'      => collect($value)->toJson(),
                'sort_order' => $key,
                'status'     => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            if ( ! $saved) {
                return false;
            }
        }

        return true;
    }

}
