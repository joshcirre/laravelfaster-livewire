<?php

use App\Helpers\Cart;
use Livewire\Component;

new class extends Component
{
    public string $productSlug;
    public ?string $message = null;

    public function addToCart(): void
    {
        $cart = Cart::get();

        $existingItem = collect($cart)->firstWhere('productSlug', $this->productSlug);

        if ($existingItem) {
            // Increment quantity if item already exists
            $newCart = collect($cart)->map(function ($item) {
                if ($item['productSlug'] === $this->productSlug) {
                    $item['quantity']++;
                }

                return $item;
            })->toArray();
        } else {
            // Add new item with quantity 1
            $newCart = array_merge($cart, [
                [
                    'productSlug' => $this->productSlug,
                    'quantity' => 1,
                ],
            ]);
        }

        Cart::update($newCart);

        $this->message = 'Item added to cart';

        // Dispatch event to update cart badge
        $this->dispatch('cart-updated');
    }
};
?>

<form wire:submit="addToCart" class="flex flex-col gap-2">
    <button
        type="submit"
        class="max-w-[150px] rounded-[2px] bg-accent1 px-5 py-1 text-sm font-semibold text-white"
        wire:loading.attr="disabled"
    >
        Add to cart
    </button>

    <p wire:loading>Adding to cart...</p>

    @if ($message && !$errors->any())
        <p wire:loading.remove>{{ $message }}</p>
    @endif
</form>
