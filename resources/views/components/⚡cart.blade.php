<?php

use App\Helpers\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public int $totalQuantity = 0;

    public function mount(): void
    {
        $this->updateCartCount();
    }

    #[On('cart-updated')]
    public function updateCartCount(): void
    {
        $cart = Cart::get();
        $this->totalQuantity = array_reduce(
            $cart,
            fn ($carry, $item) => $carry + $item['quantity'],
            0
        );
    }
};
?>

<div>
    @if ($totalQuantity > 0)
        <div class="absolute -right-3 -top-1 rounded-full bg-accent2 px-1 text-xs text-accent1">
            {{ $totalQuantity }}
        </div>
    @endif
</div>
