<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>{{ $title ?? 'Admin • BeautyBliss' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-purple-50 to-pink-50 text-gray-900">
  <div class="md:grid md:grid-cols-[260px_1fr] md:min-h-screen">
    {{-- Sidebar --}}
    <aside class="bg-gradient-to-b from-purple-700 via-purple-600 to-pink-600 text-white md:sticky md:top-0 md:h-screen">
      <div class="px-5 py-4 flex items-center justify-between md:block">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
          <div class="h-9 w-9 rounded-2xl bg-white/10 grid place-items-center font-extrabold">B</div>
          <div class="hidden md:block leading-tight">
            <div class="text-white font-extrabold">BeautyBliss</div>
            <div class="text-white/70 text-xs">Admin Panel</div>
          </div>
        </a>
        <button id="adminNavToggle" class="md:hidden text-white/90 p-2 rounded-lg hover:bg-white/10" aria-label="Toggle sidebar">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

      <nav id="adminNav" class="px-3 pb-4 hidden md:block">
        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10' : '' }}">Dashboard</a>
        <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.products.*') ? 'bg-white/10' : '' }}">Products</a>
        <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.categories.*') ? 'bg-white/10' : '' }}">Categories</a>
        <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.orders.*') ? 'bg-white/10' : '' }}">Customer Orders</a>

        <div class="mt-4 border-t border-white/10 pt-3">
          <a href="{{ route('shop.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10">View Storefront</a>
          <form action="{{ route('logout') }}" method="POST" class="px-3 pt-2">
            @csrf
            <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-white/10">Logout</button>
          </form>
        </div>
      </nav>
    </aside>

    {{-- Main --}}
    <main class="p-4 md:p-8">
      @if (session('success'))
        <div class="mb-6 bg-white border border-green-200 text-green-700 rounded-2xl p-4 shadow-sm">
          {{ session('success') }}
        </div>
      @endif
      @if ($errors->any())
        <div class="mb-6 bg-white border border-pink-200 text-pink-700 rounded-2xl p-4 shadow-sm">
          <ul class="list-disc ml-5 space-y-1">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  <script>
    (function () {
      const btn = document.getElementById('adminNavToggle');
      const nav = document.getElementById('adminNav');
      if (!btn || !nav) return;
      btn.addEventListener('click', () => nav.classList.toggle('hidden'));
      document.addEventListener('click', (e) => {
        if (!nav.contains(e.target) && !btn.contains(e.target) && !nav.classList.contains('hidden')) {
          nav.classList.add('hidden');
        }
      });
    })();
  </script>
</body>
</html>
