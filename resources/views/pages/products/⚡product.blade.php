<?php

use App\Models\Collection;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    public string $productSlug;
    public string $categorySlug;
    public string $subcategorySlug;

    public function mount(string $category, string $subcategory, string $product): void
    {
        $this->categorySlug = $category;
        $this->subcategorySlug = $subcategory;
        $this->productSlug = $product;
    }

    #[Computed(cache: true, seconds: 7200)]
    public function collections()
    {
        return Collection::with('categories')->get();
    }

    #[Computed]
    public function product()
    {
        return cache()->remember(
            "product:{$this->productSlug}",
            7200,
            fn () => Product::with(['subcategory.subcollection.category'])
                ->where('slug', $this->productSlug)
                ->firstOrFail()
        );
    }

    #[Computed]
    public function relatedProducts()
    {
        return cache()->remember(
            "related-products:{$this->subcategorySlug}:{$this->productSlug}",
            7200,
            fn () => Product::where('subcategory_slug', $this->subcategorySlug)
                ->where('slug', '!=', $this->productSlug)
                ->limit(20)
                ->get()
        );
    }
};
?>

<x-sidebar-layout :collections="$this->collections">
    <div class="container p-4">
        <h1 class="border-t-2 pt-1 text-xl font-bold text-accent1">
            {{ $this->product->name }}
        </h1>

        <div class="flex flex-col gap-2">
            <div class="flex flex-row gap-2">
                @if ($this->product->image_url)
                    <img
                        src="{{ $this->product->image_url }}"
                        alt="{{ $this->product->name }}"
                        class="h-56 w-56 flex-shrink-0 border-2 md:h-64 md:w-64"
                        loading="eager"
                    />
                @endif
                <p class="flex-grow text-base">{{ $this->product->description }}</p>
            </div>

            <p class="text-xl font-bold">
                ${{ number_format($this->product->price, 2) }}
            </p>

            <livewire:components::add-to-cart :productSlug="$this->product->slug" />
        </div>

        @if ($this->relatedProducts->count() > 0)
            <div class="pt-8">
                <h2 class="text-lg font-bold text-accent1">
                    Explore more products
                </h2>
                <div class="flex flex-row flex-wrap gap-2">
                    @foreach ($this->relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-sidebar-layout>
