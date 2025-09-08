@extends('layouts.app')

@section('content')
{{-- Top bar: Back + breadcrumb --}}
<div class="mb-6 flex items-center justify-between gap-3">
  <a href="{{ url()->previous() ?: route('shop.index') }}"
     class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-purple-800 ring-1 ring-purple-200 hover:bg-purple-50 hover:ring-purple-300 transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Back
  </a>

  <nav class="hidden md:block text-xs text-gray-500">
    <a href="{{ route('shop.index') }}" class="hover:text-purple-700">Shop</a>
    @if(optional($product->category)->id)
      <span class="mx-2">/</span>
      <a href="{{ route('shop.index', ['category_id' => $product->category_id]) }}" class="hover:text-purple-700">
        {{ $product->category->name }}
      </a>
    @endif
    <span class="mx-2">/</span>
    <span class="text-gray-600">
      {{ \Illuminate\Support\Str::limit($product->name, 48) }}
    </span>
  </nav>
</div>

<div class="grid md:grid-cols-2 gap-8">
  {{-- Gallery (unchanged) --}}
  <div class="bg-white rounded-2xl shadow p-4">
    <div id="zoomWrap"
         class="group relative overflow-hidden rounded-2xl ring-1 ring-purple-100 bg-gradient-to-br from-purple-100 to-pink-100">
      @if($product->featured_image_url)
        <img id="mainImage"
             src="{{ $product->featured_image_url }}"
             alt="{{ $product->name }}"
             class="w-full h-full object-cover transition-transform duration-300 ease-out group-hover:scale-[1.8] will-change-transform"
             style="transform-origin:center center" />
      @else
        <div class="aspect-[4/3] grid place-items-center text-purple-400">No Image</div>
      @endif
      <div class="pointer-events-none absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/5 to-transparent"></div>
      <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/5 to-transparent"></div>
    </div>

    @if($product->images->count() > 0)
      <div class="mt-4 flex gap-3 overflow-x-auto pb-1">
        @foreach($product->images as $img)
          @php $src = asset('storage/'.$img->path); @endphp
          <button type="button"
                  class="thumb-btn shrink-0 relative rounded-xl overflow-hidden ring-1 ring-purple-100 hover:ring-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-500"
                  data-src="{{ $src }}"
                  aria-label="View image">
            <img src="{{ $src }}" class="h-20 w-20 md:h-24 md:w-24 object-cover" alt="Thumbnail" />
            <span class="pointer-events-none absolute inset-0 shadow-inner shadow-black/10"></span>
          </button>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Details --}}
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-purple-800">{{ $product->name }}</h1>

    <div class="mt-2 flex items-center gap-3">
      <span class="text-2xl font-bold text-pink-600">${{ number_format($product->price, 2) }}</span>

      {{-- Live stock badges --}}
      <span id="stockBadge"
            class="text-xs px-2 py-1 rounded-full {{ $product->stock>0 ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 'bg-gray-100 text-gray-600 ring-1 ring-gray-200' }}">
        {{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}
      </span>
      <span id="stockLeft" class="text-xs text-gray-500">
        @if($product->stock > 0)
          • <strong>{{ $product->stock }}</strong> left
        @endif
      </span>
    </div>

    @if($product->short_description)
      <p class="mt-3 text-sm text-gray-600">{{ $product->short_description }}</p>
    @endif

    <div class="mt-5 prose prose-p:leading-relaxed max-w-none text-gray-700">
      {!! nl2br(e($product->description)) !!}
    </div>

    @auth
      <form id="addToCartForm"
            action="{{ route('cart.add', $product->id) }}"
            method="POST"
            class="mt-6 flex flex-wrap items-center gap-3"
            data-product-id="{{ $product->id }}">
        @csrf

        <label class="inline-flex items-center gap-2">
          <span class="text-sm text-gray-600">Qty</span>
          <input id="qtyInput"
                 type="number"
                 name="qty"
                 value="1"
                 min="1"
                 max="{{ max(1, $product->stock) }}"
                 class="w-24 border rounded-lg px-3 py-2 focus:ring-pink-500 focus:border-pink-500"
                 {{ $product->stock < 1 ? 'disabled' : '' }}>
        </label>

        <button id="addToCartBtn"
                class="inline-flex items-center gap-2 bg-pink-500 hover:bg-pink-600 text-white px-5 py-2.5 rounded-xl shadow transition disabled:opacity-50 disabled:cursor-not-allowed"
                {{ $product->stock < 1 ? 'disabled' : '' }}>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m4-9l2 9"/></svg>
          Add to Cart
        </button>

        <span id="qtyHint" class="text-xs text-gray-500"></span>
      </form>
    @else
      <p class="mt-6 text-sm text-gray-600">
        Please <a href="{{ route('login') }}" class="text-pink-600 underline">login</a> to add to cart.
      </p>
    @endauth
  </div>
</div>

{{-- Suggestions (rails) --}}
@if(($related->count() ?? 0) + ($youMayAlsoLike->count() ?? 0) + ($recentlyViewed->count() ?? 0) > 0)
  <div class="mt-12 space-y-10">
    @if(!empty($related) && $related->count() > 0)
      @include('shop.partials.product-rail', [
        'title' => 'Related in this category',
        'subtitle' => 'Fresh picks curated for you',
        'products' => $related
      ])
    @endif

    @if(!empty($youMayAlsoLike) && $youMayAlsoLike->count() > 0)
      @include('shop.partials.product-rail', [
        'title' => 'You may also like',
        'subtitle' => 'Similar vibe, different twist',
        'products' => $youMayAlsoLike
      ])
    @endif

    @if(!empty($recentlyViewed) && $recentlyViewed->count() > 0)
      @include('shop.partials.product-rail', [
        'title' => 'Recently viewed',
        'subtitle' => 'Because you looked at these',
        'products' => $recentlyViewed
      ])
    @endif
  </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Thumbnails
  const mainImage = document.getElementById('mainImage');
  const thumbs = document.querySelectorAll('.thumb-btn');
  thumbs.forEach(btn => {
    btn.addEventListener('click', () => {
      const next = btn.dataset.src;
      if (!mainImage || !next) return;
      mainImage.style.opacity = '0';
      setTimeout(() => { mainImage.src = next; mainImage.style.opacity = '1'; }, 120);
      thumbs.forEach(b => b.classList.remove('ring-2','ring-pink-500'));
      btn.classList.add('ring-2','ring-pink-500');
    });
  });

  // Hover zoom
  const wrap = document.getElementById('zoomWrap');
  if (wrap && mainImage) {
    const leave = () => { mainImage.style.transformOrigin = 'center center'; mainImage.style.transitionDuration = '300ms'; };
    wrap.addEventListener('mousemove', (e) => {
      const r = wrap.getBoundingClientRect();
      const x = ((e.clientX - r.left) / r.width) * 100;
      const y = ((e.clientY - r.top) / r.height) * 100;
      mainImage.style.transformOrigin = `${x}% ${y}%`;
    });
    wrap.addEventListener('mouseleave', leave);
    let zoomed = false;
    wrap.addEventListener('touchstart', (e) => {
      zoomed = !zoomed;
      mainImage.style.transform = zoomed ? 'scale(1.8)' : 'scale(1)';
      if (zoomed && e.touches[0]) {
        const r = wrap.getBoundingClientRect();
        const x = ((e.touches[0].clientX - r.left) / r.width) * 100;
        const y = ((e.touches[0].clientY - r.top) / r.height) * 100;
        mainImage.style.transformOrigin = `${x}% ${y}%`;
      }
    }, {passive:true});
    wrap.addEventListener('touchmove', (e) => {
      if (!zoomed || !e.touches[0]) return;
      const r = wrap.getBoundingClientRect();
      const x = ((e.touches[0].clientX - r.left) / r.width) * 100;
      const y = ((e.touches[0].clientY - r.top) / r.height) * 100;
      mainImage.style.transformOrigin = `${x}% ${y}%`;
    }, {passive:true});
  }

  // Live stock enforcement
  const productId  = document.getElementById('addToCartForm')?.dataset.productId;
  const qtyInput   = document.getElementById('qtyInput');
  const addBtn     = document.getElementById('addToCartBtn');
  const stockBadge = document.getElementById('stockBadge');
  const stockLeft  = document.getElementById('stockLeft');
  const qtyHint    = document.getElementById('qtyHint');

  async function fetchStock() {
    if (!productId) return null;
    try {
      const res = await fetch("{{ route('products.stock', ':id') }}".replace(':id', productId), { cache: 'no-store' });
      if (!res.ok) return null;
      const data = await res.json();
      return typeof data.stock === 'number' ? data.stock : null;
    } catch { return null; }
  }

  function clampQty(max) {
    if (!qtyInput) return;
    const val = parseInt(qtyInput.value || '1', 10);
    const clamped = Math.max(1, Math.min(val, Math.max(1, max)));
    if (val !== clamped) qtyInput.value = clamped;
    qtyInput.max = Math.max(1, max);
    qtyHint.textContent = max > 0 ? `Max ${max}` : '';
  }

  function renderStockUI(stock) {
    if (!stockBadge || !stockLeft) return;
    if (stock > 0) {
      stockBadge.textContent = 'In stock';
      stockBadge.className = 'text-xs px-2 py-1 rounded-full bg-green-50 text-green-700 ring-1 ring-green-200';
      stockLeft.innerHTML = ` • <strong>${stock}</strong> left`;
      addBtn?.removeAttribute('disabled');
      qtyInput?.removeAttribute('disabled');
    } else {
      stockBadge.textContent = 'Out of stock';
      stockBadge.className = 'text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 ring-1 ring-gray-200';
      stockLeft.textContent = '';
      addBtn?.setAttribute('disabled','disabled');
      qtyInput?.setAttribute('disabled','disabled');
    }
  }

  (async () => {
    const s = await fetchStock();
    if (s !== null) { clampQty(s); renderStockUI(s); }
  })();

  ['focus','visibilitychange'].forEach(evt => {
    document.addEventListener(evt, async () => {
      if (document.visibilityState === 'hidden') return;
      const s = await fetchStock();
      if (s !== null) { clampQty(s); renderStockUI(s); }
    });
  });
  setInterval(async () => {
    const s = await fetchStock();
    if (s !== null) { clampQty(s); renderStockUI(s); }
  }, 20000);

  qtyInput?.addEventListener('input', async () => {
    const s = await fetchStock();
    if (s !== null) clampQty(s);
  });

  document.getElementById('addToCartForm')?.addEventListener('submit', async (e) => {
    const s = await fetchStock();
    if (s === null) return;
    const want = parseInt(qtyInput.value || '1', 10);
    if (s < 1 || want > s) {
      e.preventDefault();
      clampQty(s);
      renderStockUI(s);
      alert(s < 1 ? 'Sorry, this item is now out of stock.' : `Only ${s} left. Quantity reduced to ${qtyInput.value}.`);
    }
  });
});
</script>
@endpush
