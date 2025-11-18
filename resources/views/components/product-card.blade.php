@blaze

@props(['product'])

<a
    href="/products/{{ $product->subcategory->subcollection->category->slug }}/{{ $product->subcategory_slug }}/{{ $product->slug }}"
    class="group flex h-[130px] w-full flex-row border px-4 py-2 hover:bg-gray-100 sm:w-[250px]"
    wire:navigate.hover
>
    <div class="py-2">
        @if ($product->image_url)
            <img
                src="{{ $product->image_url }}"
                alt="{{ $product->name }}"
                class="h-auto w-12 flex-shrink-0 object-cover"
                loading="lazy"
            />
        @endif
    </div>
    <div class="px-2"></div>
    <div class="flex flex-grow flex-col items-start py-2">
        <div class="text-sm font-medium text-gray-700 group-hover:underline">
            {{ $product->name }}
        </div>
        <p class="overflow-hidden text-xs">{{ $product->description }}</p>
    </div>
</a>
