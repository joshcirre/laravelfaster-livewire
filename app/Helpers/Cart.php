<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class Cart
{
    /**
     * Get the cart items from the cookie.
     *
     * @return array<int, array{productSlug: string, quantity: int}>
     */
    public static function get(): array
    {
        $cart = Cookie::get('cart');

        if (! $cart) {
            return [];
        }

        try {
            $decoded = json_decode($cart, true);

            if (! is_array($decoded)) {
                return [];
            }

            return $decoded;
        } catch (\Exception $e) {
            \Log::error('Failed to parse cart cookie');

            return [];
        }
    }

    /**
     * Update the cart items in the cookie.
     *
     * @param  array<int, array{productSlug: string, quantity: int}>  $items
     */
    public static function update(array $items): void
    {
        Cookie::queue('cart', json_encode($items), 60 * 24 * 7); // 1 week
    }

    /**
     * Get detailed cart with product information.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function detailed()
    {
        $cart = self::get();

        if (empty($cart)) {
            return collect([]);
        }

        $productSlugs = array_map(fn ($item) => $item['productSlug'], $cart);

        $products = Product::with(['subcategory.subcollection'])
            ->whereIn('slug', $productSlugs)
            ->get();

        return $products->map(function ($product) use ($cart) {
            $cartItem = collect($cart)->firstWhere('productSlug', $product->slug);
            $product->quantity = $cartItem['quantity'] ?? 0;

            return $product;
        });
    }
}
