<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\Catalog\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GoogleAdsFeedController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $filename = 'google_ads_feed.csv';

        $response = new StreamedResponse(function () {
            $out = fopen('php://output', 'w');

            // Header red
            fputcsv($out, [
                'ID',
                'Item title',
                'Image URL',
                'Item description',
                'Item category',
                'Price',
                'Sale price',
                'Final URL',
            ], ',', '"', '');

            Product::query()
                ->active()
                ->hasStock()
                ->orderBy('id')
                ->cursor()
                ->each(function (Product $p) use ($out) {

                    // 1) ID
                    $id = (string) $p->id;

                    // 2) Naziv
                    $title = $this->cleanText($p->name);

                    // 3) Slika
                    $imageUrl = $this->encodeUrl(
                        $this->removeHrFromUrl($this->absolutizeUrl($p->image))
                    );

                    // 4) Opis
                    $description = $this->cleanText($p->description ?? '');

                    // 5) Kategorija
                    $category = $this->buildCategoryPath($p);

                    // 6) Cijena
                    $basePrice = $p->eur_price !== null ? (float) $p->eur_price : (float) $p->price;
                    $price = $this->formatPriceWithCurrency($basePrice);

                    // 7) Akcijska cijena
                    $specialBase = $p->eur_special !== null ? (float) $p->eur_special : (float) $p->special();
                    $salePrice = ($specialBase > 0 && $specialBase < $basePrice)
                        ? $this->formatPriceWithCurrency($specialBase)
                        : '';

                    // 8) Final URL
                    $finalUrl = rtrim(config('app.url'), '/') . '/' . ltrim((string) $p->url, '/');
                    $finalUrl = $this->encodeUrl($this->removeHrFromUrl($finalUrl));

                    fputcsv($out, [
                        $id,
                        $title,
                        $imageUrl,
                        $description,
                        $category,
                        $price,
                        $salePrice,
                        $finalUrl,
                    ], ',', '"', '');
                });

            fclose($out);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'inline; filename="'.$filename.'"');

        return $response;
    }

    private function cleanText(?string $text): string
    {
        $text = (string) $text;
        $text = preg_replace('/\R+/u', ' ', $text);
        $text = str_replace(';', ' ', $text);
        $text = str_replace(',', '', $text);
        $text = preg_replace('/\s{2,}/', ' ', $text);
        return trim($text);
    }

    private function formatPriceWithCurrency(float $value): string
    {
        return number_format($value, 2, '.', '') . ' EUR';
    }

    private function absolutizeUrl(?string $url): string
    {
        $url = (string) $url;
        if ($url === '') return '';
        $parts = parse_url($url);
        if (!isset($parts['scheme']) || !isset($parts['host'])) {
            return rtrim(config('app.url'), '/') . '/' . ltrim($url, '/');
        }
        return $url;
    }

    private function encodeUrl(?string $url): string
    {
        if (!$url) return '';

        $parts = parse_url($url);
        $scheme   = $parts['scheme'] ?? null;
        $host     = $parts['host']   ?? null;
        $port     = isset($parts['port']) ? ':' . $parts['port'] : '';
        $user     = $parts['user']   ?? null;
        $pass     = $parts['pass']   ?? null;
        $path     = $parts['path']   ?? '';
        $query    = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        $encodedPath = implode('/', array_map(function ($seg) {
            return rawurlencode($seg);
        }, array_filter(explode('/', $path), fn($s) => $s !== '')));

        if ($path !== '' && substr($path, 0, 1) === '/') {
            $encodedPath = '/' . $encodedPath;
        }

        $auth = '';
        if ($user) {
            $auth = $user;
            if ($pass) $auth .= ':' . $pass;
            $auth .= '@';
        }

        $origin = '';
        if ($scheme && $host) {
            $origin = $scheme . '://' . $auth . $host . $port;
        } elseif ($host) {
            $origin = '//' . $auth . $host . $port;
        }

        return $origin . $encodedPath . $query . $fragment;
    }

    /** Ukloni svaku pojavu /hr iz URL-a */
    private function removeHrFromUrl(string $url): string
    {
        return str_replace('/hr', '', $url);
    }

    private function buildCategoryPath(Product $p): string
    {
        $parent = $p->category();
        $child  = $p->subcategory();

        $segments = [];

        if ($parent) {
            $name = (property_exists($parent, 'translation') && $parent->translation)
                ? ($parent->translation->name ?? null)
                : ($parent->name ?? null);
            if ($name) $segments[] = $this->cleanText($name);
        }

        if ($child && (!$parent || $child->id !== $parent->id)) {
            $name = (property_exists($child, 'translation') && $child->translation)
                ? ($child->translation->name ?? null)
                : ($child->name ?? null);
            if ($name) $segments[] = $this->cleanText($name);
        }

        return implode(' > ', $segments);
    }
}
