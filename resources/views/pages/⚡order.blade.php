<?php

use App\Helpers\Cart;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function cartItems()
    {
        return Cart::detailed();
    }

    #[Computed]
    public function totalCost()
    {
        return $this->cartItems->reduce(
            fn ($total, $item) => $total + ($item->quantity * $item->price),
            0
        );
    }

    public function removeFromCart(string $productSlug): void
    {
        $cart = Cart::get();
        $newCart = collect($cart)->filter(
            fn ($item) => $item['productSlug'] !== $productSlug
        )->values()->toArray();

        Cart::update($newCart);

        $this->dispatch('cart-updated');
    }
};
?>

<main class="min-h-screen sm:p-4">
    <div class="container mx-auto p-1 sm:p-3">
        <div class="flex items-center justify-between border-b border-gray-200">
            <h1 class="text-2xl text-accent1">Order</h1>
        </div>

        <div class="flex grid-cols-3 flex-col gap-8 pt-4 lg:grid">
            {{-- Cart Items --}}
            <div class="col-span-2">
                @if ($this->cartItems->count() > 0)
                    <div class="pb-4">
                        <p class="font-semibold text-accent1">Delivers in 2-4 weeks</p>
                        <p class="text-sm text-gray-500">Need this sooner?</p>
                    </div>

                    <div class="flex flex-col space-y-10">
                        @foreach ($this->cartItems as $item)
                            <div class="flex flex-row items-center justify-between border-t border-gray-200 pt-4" wire:key="cart-item-{{ $item->slug }}">
                                <a
                                    href="/products/{{ $item->subcategory->subcollection->category->slug }}/{{ $item->subcategory->slug }}/{{ $item->slug }}"
                                    wire:navigate.hover
                                >
                                    <div class="flex flex-row space-x-2">
                                        <div class="flex h-24 w-24 items-center justify-center bg-gray-100">
                                            @if ($item->image_url)
                                                <img
                                                    src="{{ $item->image_url }}"
                                                    alt="Product"
                                                    loading="eager"
                                                    class="h-full w-full object-cover"
                                                />
                                            @endif
                                        </div>
                                        <div class="max-w-[100px] flex-grow sm:max-w-full">
                                            <h2 class="font-semibold">{{ $item->name }}</h2>
                                            <p class="text-sm md:text-base">{{ $item->description }}</p>
                                        </div>
                                    </div>
                                </a>
                                <div class="flex items-center justify-center md:space-x-10">
                                    <div class="flex flex-col-reverse md:flex-row md:gap-4">
                                        <p>{{ $item->quantity }}</p>
                                        <div class="flex md:block">
                                            <div class="min-w-8 text-sm md:min-w-24 md:text-base">
                                                <p>${{ number_format($item->price, 2) }} each</p>
                                            </div>
                                        </div>
                                        <div class="min-w-24">
                                            <p class="font-semibold">${{ number_format($item->quantity * $item->price, 2) }}</p>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="removeFromCart('{{ $item->slug }}')"
                                        class="text-gray-600 hover:text-gray-800"
                                    >
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No items in cart</p>
                @endif
            </div>

            {{-- Summary --}}
            <div class="space-y-4">
                <div class="rounded bg-gray-100 p-4">
                    <p class="font-semibold">
                        Merchandise ${{ number_format($this->totalCost, 2) }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Applicable shipping and tax will be added.
                    </p>
                </div>
                {{-- Place order button (simplified, no auth for now) --}}
                <button
                    type="button"
                    class="w-full rounded-[2px] bg-accent1 px-5 py-2 font-semibold text-white"
                    @if ($this->cartItems->count() === 0) disabled @endif
                >
                    Place Order
                </button>
            </div>
        </div>
    </div>
</main>
