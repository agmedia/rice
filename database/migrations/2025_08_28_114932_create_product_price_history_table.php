<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPriceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_price_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            // 'regular' = list price; 'sale' = promotional/discounted price active during a window
            $table->enum('kind', ['regular', 'sale'])->index();
            $table->decimal('price', 15, 4);
            $table->char('currency', 3)->default('EUR');
            // effective dating (closed-open intervals)
            $table->timestamp('effective_at');                 // inclusive start
            $table->timestamp('ended_at')->nullable();         // exclusive end; null = still active

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            // Fast lookups for "as of date" and 30d windows
            $table->index(['product_id','kind','effective_at']);
            $table->index(['product_id','effective_at','ended_at']);
        });

        Schema::table('products', function (Blueprint $t) {
            $t->decimal('lowest_price_30d', 12, 2)->nullable()->after('price');
            $t->date('lowest_price_30d_since')->nullable()->after('lowest_price_30d');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_price_history');
    }
}
