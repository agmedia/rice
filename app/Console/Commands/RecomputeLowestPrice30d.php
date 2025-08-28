<?php

namespace App\Console\Commands;

use App\Models\Front\Catalog\Product;
use Illuminate\Console\Command;

class RecomputeLowestPrice30d extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recompute:lowest_price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nightly job to recompute lowest price for all products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Product::query()->select('id')->chunkById(500, function ($chunk) {
            foreach ($chunk as $p) {
                $min = $p->lowestPriceInWindow(30);
                $when = $min
                    ? optional($p->prices()
                                 ->where('price', $min)
                                 ->orderByDesc('effective_at')
                                 ->first())->effective_at?->toDateString()
                    : null;

                $p->forceFill([
                    'lowest_price_30d'       => $min,
                    'lowest_price_30d_since' => $when,
                ])->saveQuietly();
            }
        });

        return 1;
    }
}
