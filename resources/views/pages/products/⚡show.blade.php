<?php

use App\Models\Collection;
use App\Models\Product;
use App\Models\Subcategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    public string $categorySlug;
    public string $subcategorySlug;

    public function mount(string $category, string $subcategory): void
    {
        $this->categorySlug = $category;
        $this->subcategorySlug = $subcategory;
    }

    #[Computed(cache: true, seconds: 7200)]
    public function collections()
    {
        return Collection::with('categories')->get();
    }

    #[Computed]
    public function subcategory()
    {
        return cache()->remember(
            "subcategory:{$this->subcategorySlug}",
            7200,
            fn () => Subcategory::with('subcollection.category')
                ->where('slug', $this->subcategorySlug)
                ->firstOrFail()
        );
    }

    #[Computed]
    public function products()
    {
        return cache()->remember(
            "subcategory-products:{$this->subcategorySlug}",
            7200,
            fn () => Product::where('subcategory_slug', $this->subcategorySlug)
                ->orderBy('name')
                ->get()
        );
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('pages.products.⚡show');
    }
};
?>

<x-sidebar-layout :collections="$this->collections">
    <div class="container mx-auto p-4">
        @if ($this->products->count() > 0)
            <h1 class="mb-2 border-b-2 text-sm font-bold">
                {{ number_format($this->products->count()) }} {{ $this->products->count() === 1 ? 'Product' : 'Products' }}
            </h1>
        @else
            <p>No products for this subcategory</p>
        @endif

        <div class="flex flex-row flex-wrap gap-2">
            @foreach ($this->products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</x-sidebar-layout>
