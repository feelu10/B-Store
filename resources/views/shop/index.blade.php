@extends('layouts.app')

@section('content')
{{-- Icons (optional) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  {{-- Header --}}
  <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
    <div>
      <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">New Arrivals</h1>
      <p class="text-sm text-pink-700">Curated beauty essentials just for you.</p>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" role="search" aria-label="Product filters"
      class="bg-white/90 backdrop-blur rounded-2xl border border-gray-100 shadow-sm p-4 md:p-5 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-3 md:gap-4 items-end">
        {{-- Search --}}
        <div class="md:col-span-5">
          <label for="q" class="block text-xs font-medium text-gray-600 mb-1 md:mb-2">
            Search
          </label>
          <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
              <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input id="q" name="q" value="{{ $q ?? '' }}" placeholder="Search products…"
                  class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"/>
            {{-- clear (x) --}}
            @if(!empty($q))
              <button type="button" id="clearSearch"
                      class="absolute inset-y-0 right-2.5 my-auto h-7 w-7 grid place-items-center rounded-full hover:bg-gray-100 text-gray-400"
                      aria-label="Clear search">
                <i class="fa-solid fa-xmark text-xs"></i>
              </button>
            @endif
          </div>
        </div>

        {{-- Category --}}
        <div class="md:col-span-3">
          <label for="category_id" class="block text-xs font-medium text-gray-600 mb-1 md:mb-2">
            Category
          </label>
          <div class="relative">
            <select id="category_id" name="category_id"
                    class="w-full appearance-none py-2.5 pl-3 pr-9 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
              <option value="">All categories</option>
              @foreach(($categories ?? collect()) as $cat)
                <option value="{{ $cat->id }}" @selected(($categoryId ?? null) == $cat->id)>{{ $cat->name }}</option>
              @endforeach
            </select>
            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
              <i class="fa-solid fa-chevron-down text-xs"></i>
            </span>
          </div>
        </div>

        {{-- Sort --}}
        <div class="md:col-span-3">
          <label for="sort" class="block text-xs font-medium text-gray-600 mb-1 md:mb-2">
            Sort by
          </label>
          <div class="relative">
            <select id="sort" name="sort"
                    class="w-full appearance-none py-2.5 pl-3 pr-9 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
              <option value="latest"     @selected(($sort ?? 'latest') === 'latest')>Newest</option>
              <option value="oldest"     @selected(($sort ?? '') === 'oldest')>Oldest</option>
              <option value="price_low"  @selected(($sort ?? '') === 'price_low')>Price: Low → High</option>
              <option value="price_high" @selected(($sort ?? '') === 'price_high')>Price: High → Low</option>
              <option value="name_az"    @selected(($sort ?? '') === 'name_az')>Name: A → Z</option>
              <option value="name_za"    @selected(($sort ?? '') === 'name_za')>Name: Z → A</option>
            </select>
            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
              <i class="fa-solid fa-chevron-down text-xs"></i>
            </span>
          </div>
        </div>

        {{-- Actions --}}
        <div class="md:col-span-1 flex items-center gap-2 md:justify-end">
          <button
            class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white text-sm font-semibold shadow">
            <i class="fa-solid fa-filter"></i>
            Apply
          </button>

          <a href="{{ route('shop.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium">
            <i class="fa-solid fa-rotate-right"></i>
            Reset
          </a>
        </div>
      </div>

      {{-- Active chips --}}
      @if(($q ?? '') !== '' || ($categoryId ?? null) || ($sort ?? 'latest') !== 'latest')
        <div class="mt-3 flex flex-wrap items-center gap-2">
          @if(($q ?? '') !== '')
            <a href="{{ request()->fullUrlWithQuery(['q'=>null]) }}"
              class="text-xs inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">
              <i class="fa-solid fa-xmark"></i> “{{ $q }}”
            </a>
          @endif
          @if(($categoryId ?? null))
            @php $catName = optional(($categories ?? collect())->firstWhere('id',$categoryId))->name; @endphp
            <a href="{{ request()->fullUrlWithQuery(['category_id'=>null]) }}"
              class="text-xs inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200">
              <i class="fa-solid fa-xmark"></i> {{ $catName }}
            </a>
          @endif
          @if(($sort ?? 'latest') !== 'latest')
            <a href="{{ request()->fullUrlWithQuery(['sort'=>null]) }}"
              class="text-xs inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-pink-100 text-pink-800 hover:bg-pink-200">
              <i class="fa-solid fa-xmark"></i> {{ str_replace('_',' ', $sort) }}
            </a>
          @endif
        </div>
      @endif
    </form>

  {{-- Grid --}}
  @if($products->count())
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
      @foreach($products as $p)
        <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden">
          <a href="{{ route('shop.show', $p->slug) }}" class="block focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
            <div class="relative aspect-[4/3] bg-gradient-to-br from-purple-50 to-pink-50 overflow-hidden">
              @if($p->featured_image_url)
                <img src="{{ $p->featured_image_url }}" alt="{{ $p->name }}"
                     loading="lazy"
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"/>
              @else
                <div class="absolute inset-0 grid place-items-center text-purple-300">
                  <i class="fa-regular fa-image text-2xl"></i>
                </div>
              @endif
              {{-- Stock badge --}}
              <div class="absolute top-2 left-2">
                @if(($p->stock ?? 0) > 0)
                  <span class="inline-flex items-center gap-1 text-[11px] px-2 py-1 rounded-full bg-green-50 text-green-700 ring-1 ring-green-200">
                    <i class="fa-solid fa-circle-check"></i> In stock
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-600 ring-1 ring-gray-200">
                    <i class="fa-solid fa-circle-xmark"></i> Out of stock
                  </span>
                @endif
              </div>
            </div>
            <div class="p-3 sm:p-4">
              <h3 class="text-sm sm:text-base font-semibold text-gray-900 line-clamp-2">{{ $p->name }}</h3>
              @if($p->short_description)
                <p class="mt-1 text-xs text-gray-500 line-clamp-2">{{ $p->short_description }}</p>
              @endif
              <div class="mt-2 flex items-center justify-between">
                <div class="text-base sm:text-lg font-bold text-pink-600">${{ number_format($p->price, 2) }}</div>
                @if($p->category?->name)
                  <span class="text-[11px] px-2 py-1 rounded-full bg-purple-100 text-purple-800">{{ $p->category->name }}</span>
                @endif
              </div>
            </div>
          </a>

          {{-- Quick add (auth only) --}}
          @auth
          <div class="px-3 sm:px-4 pb-4">
            <form action="{{ route('cart.add', $p->id) }}" method="POST" class="flex items-center gap-2">
              @csrf
              <input type="number" name="qty" value="1" min="1"
                     max="{{ max(1, (int)($p->stock ?? 0)) }}"
                     class="w-16 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                     {{ ($p->stock ?? 0) < 1 ? 'disabled' : '' }}>
              <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium shadow disabled:opacity-50 disabled:cursor-not-allowed"
                      {{ ($p->stock ?? 0) < 1 ? 'disabled' : '' }}>
                <i class="fa-solid fa-cart-plus"></i>
                Add to Cart
              </button>
            </form>
          </div>
          @endauth
        </div>
      @endforeach
    </div>

    {{-- Pagination (preserves filters) --}}
    <div class="mt-8">
      {{ $products->onEachSide(1)->links('pagination::tailwind') }}
    </div>
  @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center">
      <p class="text-gray-600">No products found. Try changing your filters.</p>
    </div>
  @endif
</div>

@push('scripts')
<script>
  // Auto-submit filters on md+ screens
  (function () {
    const form = document.currentScript.closest('body').querySelector('form[method="GET"]');
    if (!form) return;
    const mq = window.matchMedia('(min-width: 768px)');
    const submit = () => form.requestSubmit();

    if (mq.matches) {
      ['category_id','sort'].forEach(id => {
        const el = form.querySelector('#' + id);
        if (el) el.addEventListener('change', submit);
      });
      const search = form.querySelector('#q');
      if (search) {
        let t;
        search.addEventListener('input', () => { clearTimeout(t); t = setTimeout(submit, 450); });
      }
    }
  })();
</script>
<script>
  // Clear search button (if rendered)
  document.getElementById('clearSearch')?.addEventListener('click', () => {
    const input = document.getElementById('q');
    if (!input) return;
    input.value = '';
    input.form?.requestSubmit();
  });

  // Auto-submit on desktop/tablet for quick UX
  (function () {
    const form = document.currentScript.closest('body').querySelector('form[role="search"]');
    if (!form) return;
    const mq = window.matchMedia('(min-width: 768px)');
    const submit = () => form.requestSubmit();

    if (mq.matches) {
      ['category_id','sort'].forEach(id => {
        const el = form.querySelector('#' + id);
        el?.addEventListener('change', submit);
      });

      const search = form.querySelector('#q');
      if (search) {
        let t;
        search.addEventListener('input', () => { clearTimeout(t); t = setTimeout(submit, 450); });
      }
    }
  })();
</script>
@endpush
@endsection
