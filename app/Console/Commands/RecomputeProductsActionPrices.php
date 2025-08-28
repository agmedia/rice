<?php

namespace App\Console\Commands;

use App\Models\Front\Catalog\Product;
use Illuminate\Console\Command;

class RecomputeProductsActionPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recompute:product_action_prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nightly job to recompute correct 2 digit prices for all products';

    // tune if needed
    public int $timeout = 1800;
    public int $batchSize = 1000;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        // Only rows where rounding would change the value
        $base = DB::table('products')
                  ->select('id')
                  ->whereNotNull('special')
                  ->whereRaw('special <> ROUND(special, 2)');

        $base->orderBy('id')->chunkById($this->batchSize, function ($rows) {
            $ids = $rows->pluck('id')->all();

            // Single SQL per chunk; updates updated_at
            DB::table('products')
              ->whereIn('id', $ids)
              ->update([
                  'special'    => DB::raw('ROUND(special, 2)'),
                  'updated_at' => now(),
              ]);
        });
    }
}
