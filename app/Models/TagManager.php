<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Models\Back\Orders\Order;
use App\Models\Front\Catalog\Product;
use Darryldecode\Cart\CartCollection;

/**
 * Centralizirani GA4 TagManager
 */
class TagManager
{
    /* =========================================================
     |  JAVNE METODE — VIEW / LIST / CLICK / CART / CHECKOUT
     |========================================================= */

    /**
     * PDP: view_item payload za jedan proizvod.
     */
    public static function viewItem(Product $product): array
    {
        return [
            'event'     => 'view_item',
            'ecommerce' => [
                'items' => [ static::getGoogleProductDataLayer($product) ],
            ],
        ];
    }

    /**
     * PLP: view_item_list payload za listu proizvoda.
     * $listName npr. naziv kategorije.
     */
    public static function viewItemList(iterable $products, string $listName = ''): array
    {
        $items    = [];
        $position = 1;

        foreach ($products as $p) {
            $item = static::getGoogleProductDataLayer($p);
            if ($listName !== '') {
                $item['item_list_name'] = $listName;
            }
            $item['index']   = $position++; // preporučena pozicija u listi
            $items[]         = $item;
        }

        return [
            'event'     => 'view_item_list',
            'ecommerce' => [
                'item_list_name' => $listName,
                'items'          => $items,
            ],
        ];
    }

    /**
     * Klik na proizvod u listi (select_item).
     */
    public static function selectItem(Product $product, string $listName = '', int $position = 1): array
    {
        $item = static::getGoogleProductDataLayer($product);
        if ($listName !== '') {
            $item['item_list_name'] = $listName;
        }
        $item['index'] = $position;

        return [
            'event'     => 'select_item',
            'ecommerce' => [
                'item_list_name' => $listName,
                'items'          => [ $item ],
            ],
        ];
    }

    /**
     * Dodavanje u košaricu (add_to_cart).
     */
    public static function addToCart(Product $product, int $qty = 1): array
    {
        $item            = static::getGoogleProductDataLayer($product);
        $item['quantity'] = $qty;

        return [
            'event'     => 'add_to_cart',
            'ecommerce' => [
                'currency' => 'EUR',
                'value'    => static::fmt(static::toFloat($product->main_price) * $qty),
                'items'    => [ $item ],
            ],
        ];
    }

    /**
     * Uklanjanje iz košarice (remove_from_cart).
     */
    public static function removeFromCart(Product $product, int $qty = 1): array
    {
        $item            = static::getGoogleProductDataLayer($product);
        $item['quantity'] = $qty;

        return [
            'event'     => 'remove_from_cart',
            'ecommerce' => [
                'currency' => 'EUR',
                'value'    => static::fmt(static::toFloat($product->main_price) * $qty),
                'items'    => [ $item ],
            ],
        ];
    }

    /**
     * Početak checkouta (begin_checkout).
     * Ako već imaš kolekciju iz košarice, možeš je proslijediti — koristi getGoogleCartDataLayer().
     */
    public static function beginCheckout(array $cart_collection, float $cart_total): array
    {
        $items = static::getGoogleCartDataLayer($cart_collection);

        return [
            'event'     => 'begin_checkout',
            'ecommerce' => [
                'currency' => 'EUR',
                'value'    => static::fmt($cart_total),
                'items'    => $items,
            ],
        ];
    }

    /**
     * Purchase (thank-you) — ostavljam tvoju postojeću metodu kompatibilnom.
     * getGoogleSuccessDataLayer(Order $order) i dalje radi, ali sada
     * koristi poboljšano formatiranje.
     */
    public static function getGoogleSuccessDataLayer(Order $order)
    {
        $products = [];
        $shipping = 0.0;
        $tax      = 0.0;

        foreach ($order->products as $product) {
            $products[] = static::getGoogleProductDataLayer($product->real);
        }

        foreach ($order->totals()->get() as $total) {
            // Pretpostavka: subtotal s PDV-om 5%, shipping s PDV-om 25% (tvoja originalna računica)
            if ($total->code == 'subtotal') {
                $tax += $total->value - ($total->value / 1.05);
            }
            if ($total->code == 'shipping') {
                $tax      += $total->value - ($total->value / 1.25);
                $shipping = $total->value;
            }
        }

        return [
            'event'     => 'purchase',
            'ecommerce' => [
                'transaction_id' => (string) $order->id,
                'affiliation'    => 'Rise Kakis webshop',
                'value'          => static::fmt($order->total),
                'tax'            => static::fmt($tax),
                'shipping'       => static::fmt($shipping),
                'currency'       => 'EUR',
                'items'          => $products,
            ],
        ];
    }

    /* =========================================================
     |  MAPIRANJE ARTIKLA (koristi se svugdje)
     |========================================================= */

    /**
     * GA4 item objekt za proizvod (per-item fields).
     * Napomena: 'discount' je IZNOS popusta po artiklu (ne postotak).
     */
    public static function getGoogleProductDataLayer(Product $product): array
    {
        // Cijena i popust kao iznos po artiklu
        $price         = static::toFloat($product->main_price);
        $special       = static::toFloat($product->main_special ?? 0);
        $discountValue = 0.0;

        if ($special > 0 && $price > $special) {
            $discountValue = $price - $special; // GA4 očekuje iznos popusta, ne %
        } else {
            // Ako i dalje želiš računati iznos preko helpera (ako helper vraća %),
            // otkomentiraj:
            // $percent = (float) Helper::calculateDiscount($price, $special); // npr. 15 (%)
            // $discountValue = max(0.0, $price * ($percent / 100));
        }

        return [
            'item_id'        => $product->sku ?: (string) $product->id,
            'item_name'      => $product->name,
            'price'          => static::fmt($price),
            'currency'       => 'EUR',
            'discount'       => static::fmt($discountValue),
            'item_category'  => $product->category() ? $product->category()->title : '',
            'item_category2' => $product->subcategory() ? $product->subcategory()->title : '',
            'quantity'       => 1,
            // Dodatna polja ako ih imaš:
            // 'item_brand'   => $product->brand?->name ?? '',
            // 'item_variant' => $product->sku ?: '',
        ];
    }

    /**
     * Cart -> GA4 items (već koristiš associatedModel->dataLayer iz košarice).
     */
    public static function getGoogleCartDataLayer(array $cart_collection): array
    {
        $items = [];

        foreach ($cart_collection['items'] as $item) {
            // Pretpostavka: $item->associatedModel->dataLayer vraća GA4-kompatibilan item
            // (npr. iz getGoogleProductDataLayer + quantity).
            $items[] = $item->associatedModel->dataLayer;
        }

        return $items;
    }

    /* =========================================================
     |  POMOĆNE — sigurno rukovanje brojevima
     |========================================================= */

    private static function toFloat($value): float
    {
        // Sigurna normalizacija: "1.234,56" -> 1234.56, "1234,50" -> 1234.5
        if (is_string($value)) {
            $v = preg_replace('/\s+/', '', $value);
            // prvo uklonimo tisućice
            $v = str_replace('.', '', $v);
            // decimalni zarez pretvaramo u točku
            $v = str_replace(',', '.', $v);
            return (float) $v;
        }
        return (float) $value;
    }

    private static function fmt(float $n): float
    {
        // Vraćamo BROJ (ne string) za GA4. Zaokruženo na 2 decimale.
        return round($n, 2);
    }
}
