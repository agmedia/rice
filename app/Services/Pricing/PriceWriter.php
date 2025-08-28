<?php

namespace App\Services\Pricing;

use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Catalog\Product\ProductPriceHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PriceWriter
{

    public function setPrice(Product $product, float $amount, string $kind = 'regular', ?Carbon $effectiveAt = null): ProductPriceHistory
    {
        $effectiveAt = $effectiveAt ?: now();

        return DB::transaction(function () use ($product, $amount, $kind, $effectiveAt) {
            // Close currently-open row (if any)
            ProductPriceHistory::query()
                               ->where('product_id', $product->id)
                               ->where('kind', $kind)
                               ->whereNull('ended_at')
                               ->update(['ended_at' => $effectiveAt]);

            // Insert new row
            return ProductPriceHistory::create([
                'product_id'   => $product->id,
                'kind'         => $kind,       // 'regular' or 'sale'
                'price'        => $amount,
                'currency'     => 'EUR',
                'effective_at' => $effectiveAt
            ]);
        });
    }
}