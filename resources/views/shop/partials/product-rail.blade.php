@props(['title' => '', 'subtitle' => '', 'products' => collect()])

@if($products->count() > 0)
<section class="relative">
  <div class="mb-3 flex items-end justify-between">
    <div>
      <h2 class="text-lg font-extrabold tracking-tight text-purple-900">{{ $title }}</h2>
      @if($subtitle)
        <p class="text-xs text-purple-800/70">{{ $subtitle }}</p>
      @endif
    </div>
    <div class="hidden md:block text-xs text-purple-800/70">Scroll for more →</div>
  </div>

  <div class="relative">
    <div class="pointer-events-none absolute left-0 top-0 h-full w-6 bg-gradient-to-r from-white to-transparent"></div>
    <div class="pointer-events-none absolute right-0 top-0 h-full w-6 bg-gradient-to-l from-white to-transparent"></div>

    <div class="snap-x snap-mandatory overflow-x-auto pb-1 -mx-2 px-2">
      <ul class="flex gap-4">
        @foreach($products as $p)
          @php
            $img = $p->featured_image_url
                  ?? (optional($p->images->first())->path ? asset('storage/'.optional($p->images->first())->path) : null);
            $inStock = ($p->stock ?? 0) > 0;
            $priceFmt = '$' . number_format($p->price, 2);
          @endphp
          <li class="snap-start shrink-0 w-56">
            <div class="group relative overflow-hidden rounded-2xl border border-purple-100 bg-white shadow-sm hover:shadow transition">
              <a href="{{ route('shop.show', $p->slug) }}" class="block">
                <div class="relative aspect-[4/5] overflow-hidden">
                  @if($img)
                    <img src="{{ $img }}" alt="{{ $p->name }}"
                         class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
                  @else
                    <div class="grid h-full w-full place-items-center text-purple-300">No Image</div>
                  @endif
                  <div class="pointer-events-none absolute inset-x-0 bottom-0 h-14 bg-gradient-to-t from-black/5 to-transparent"></div>
                </div>
              </a>

              <div class="p-3">
                <a href="{{ route('shop.show', $p->slug) }}" class="line-clamp-2 text-sm font-semibold text-purple-900 hover:underline">
                  {{ $p->name }}
                </a>
                <div class="mt-1 flex items-center justify-between">
                  <div class="text-sm font-bold text-pink-600">{{ $priceFmt }}</div>
                  <span class="text-[10px] px-2 py-0.5 rounded-full {{ $inStock ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 'bg-gray-100 text-gray-600 ring-1 ring-gray-200' }}">
                    {{ $inStock ? 'In stock' : 'Out' }}
                  </span>
                </div>

                <div class="mt-3">
                  <form action="{{ route('cart.add', $p->id) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="qty" value="1">
                    <button
                      class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-pink-500 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-pink-600 disabled:opacity-50 disabled:cursor-not-allowed"
                      {{ $inStock ? '' : 'disabled' }}>
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5h10l1 2h3a1 1 0 1 1 0 2h-1.2l-1.5 8.1A3 3 0 0 1 15.3 20H8.7a3 3 0 0 1-2.9-2.4L4.3 9H3A1 1 0 1 1 3 7h3l1-2Z"/></svg>
                      Add
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</section>
@endif
