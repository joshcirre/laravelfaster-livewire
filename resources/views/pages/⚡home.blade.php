<?php

use App\Models\Collection;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    #[Computed(cache: true, seconds: 7200)]
    public function collections()
    {
        return Collection::with('categories')->get();
    }

    #[Computed(cache: true, seconds: 7200)]
    public function productCount()
    {
        return Product::count();
    }
};
?>

<x-sidebar-layout :collections="$this->collections">
    <div class="w-full p-4">
        <div class="mb-2 w-full flex-grow border-b-[1px] border-accent1 text-sm font-semibold text-black">
            Explore {{ number_format($this->productCount) }} products
        </div>

        @foreach ($this->collections as $collection)
            <div>
                <h2 class="text-xl font-semibold">{{ $collection->name }}</h2>
                <div class="flex flex-row flex-wrap justify-center gap-2 border-b-2 py-4 sm:justify-start">
                    @foreach ($collection->categories as $category)
                        <a
                            href="/products/{{ $category->slug }}"
                            class="flex w-[125px] flex-col items-center text-center"
                            wire:navigate.hover
                        >
                            @if ($category->image_url)
                                <img
                                    src="{{ $category->image_url }}"
                                    alt="{{ $category->name }}"
                                    class="mb-2 h-14 w-14 border hover:bg-accent2"
                                    loading="lazy"
                                />
                            @endif
                            <span class="text-xs">{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-sidebar-layout>
