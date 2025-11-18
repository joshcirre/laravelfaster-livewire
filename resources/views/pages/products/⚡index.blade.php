<?php

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    public string $categorySlug;

    public function mount(string $category): void
    {
        $this->categorySlug = $category;
    }

    #[Computed(cache: true, seconds: 7200)]
    public function collections()
    {
        return Collection::with('categories')->get();
    }

    #[Computed]
    public function category()
    {
        return cache()->remember(
            "category:{$this->categorySlug}",
            7200,
            fn () => Category::with(['subcollections.subcategories'])
                ->where('slug', $this->categorySlug)
                ->firstOrFail()
        );
    }

    #[Computed]
    public function productCount()
    {
        return cache()->remember(
            "category-product-count:{$this->categorySlug}",
            7200,
            fn () => Product::whereHas('subcategory.subcollection.category', function ($query) {
                $query->where('slug', $this->categorySlug);
            })->count()
        );
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('pages.products.⚡index');
    }
};
?>

<x-sidebar-layout :collections="$this->collections">
    <div class="container p-4">
        <h1 class="mb-2 border-b-2 text-sm font-bold">
            {{ number_format($this->productCount) }} {{ $this->productCount === 1 ? 'Product' : 'Products' }}
        </h1>

        <div class="space-y-4">
            @foreach ($this->category->subcollections as $subcollection)
                <div>
                    <h2 class="mb-2 border-b-2 text-lg font-semibold">
                        {{ $subcollection->name }}
                    </h2>
                    <div class="flex flex-row flex-wrap gap-2">
                        @foreach ($subcollection->subcategories as $subcategory)
                            <a
                                href="/products/{{ $categorySlug }}/{{ $subcategory->slug }}"
                                class="group flex h-full w-full flex-row gap-2 border px-4 py-2 hover:bg-gray-100 sm:w-[200px]"
                                wire:navigate.hover
                            >
                                <div class="py-2">
                                    @if ($subcategory->image_url)
                                        <img
                                            src="{{ $subcategory->image_url }}"
                                            alt="{{ $subcategory->name }}"
                                            class="h-12 w-12 flex-shrink-0 object-cover"
                                            loading="eager"
                                        />
                                    @endif
                                </div>
                                <div class="flex h-16 flex-grow flex-col items-start py-2">
                                    <div class="text-sm font-medium text-gray-700 group-hover:underline">
                                        {{ $subcategory->name }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-sidebar-layout>
