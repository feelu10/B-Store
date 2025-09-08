<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>{{ $title ?? 'BeautyBliss' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-purple-50 to-pink-50 text-gray-900 antialiased">
  <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-white rounded px-3 py-1 shadow">
    Skip to content
  </a>

  @auth
    @role('admin')
      {{-- ===================== ADMIN LAYOUT WITH SIDEBAR ===================== --}}
      <div class="md:grid md:grid-cols-[260px_1fr] md:min-h-screen">
        {{-- Sidebar --}}
        <aside class="bg-gradient-to-b from-purple-700 via-purple-600 to-pink-600 text-white md:sticky md:top-0 md:h-screen md:overflow-y-auto">
          <div class="px-5 py-4 flex items-center justify-between md:block">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
              <div class="h-9 w-9 rounded-2xl bg-white/10 grid place-items-center font-extrabold">B</div>
              <div class="hidden md:block leading-tight">
                <div class="text-white font-extrabold">BeautyBliss</div>
                <div class="text-white/70 text-xs">Admin Panel</div>
              </div>
            </a>
            <button id="adminNavToggle" class="md:hidden text-white/90 p-2 rounded-lg hover:bg-white/10" aria-label="Toggle sidebar" aria-expanded="false">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
          </div>

          <nav id="adminNav" class="px-3 pb-4 hidden md:block">
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10' : '' }}">
              Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.products.*') ? 'bg-white/10' : '' }}">
              Products
            </a>
            <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.categories.*') ? 'bg-white/10' : '' }}">
              Categories
            </a>
            <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.orders.*') ? 'bg-white/10' : '' }}">
              Customer Orders
            </a>

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
        <main class="p-4 md:p-8" id="content">
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

      {{-- Admin scripts --}}
      <script>
        (function () {
          const btn = document.getElementById('adminNavToggle');
          const nav = document.getElementById('adminNav');
          if (!btn || !nav) return;
          btn.addEventListener('click', () => {
            nav.classList.toggle('hidden');
            btn.setAttribute('aria-expanded', nav.classList.contains('hidden') ? 'false' : 'true');
          });
          document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !btn.contains(e.target) && !nav.classList.contains('hidden')) {
              nav.classList.add('hidden'); btn.setAttribute('aria-expanded','false');
            }
          });
        })();
      </script>

    @else
     {{-- ===================== CUSTOMER LAYOUT (LOGGED-IN, NON-ADMIN) ===================== --}}
    @php
      $cart = collect(session('cart', []));
      $cartCount = (int) $cart->sum('qty');
    @endphp

    <header class="sticky top-0 z-40 bg-white/70 supports-[backdrop-filter]:backdrop-blur border-b border-transparent">
      <div class="relative">
        <div class="pointer-events-none absolute inset-x-0 -bottom-px h-[2px] bg-gradient-to-r from-purple-600 via-fuchsia-500 to-pink-500/80"></div>
        <div class="max-w-7xl mx-auto px-4">
          <div class="flex items-center justify-between py-3">
            {{-- Brand --}}
            <a href="{{ route('shop.index') }}" class="flex items-center gap-2">
              <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-purple-600 to-pink-500 text-white grid place-items-center font-extrabold shadow-sm">B</div>
              <span class="hidden sm:block font-extrabold tracking-wide text-purple-900">BeautyBliss</span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-8">
              {{-- Active underline animation --}}
              <a href="{{ route('shop.index') }}"
                class="relative text-sm {{ request()->routeIs('shop.*') ? 'text-purple-900 font-semibold' : 'text-purple-800 hover:text-purple-900' }}">
                Shop
                <span class="absolute left-0 -bottom-1 h-0.5 w-full origin-left scale-x-{{ request()->routeIs('shop.*') ? '100' : '0' }} bg-gradient-to-r from-purple-600 to-pink-500 transition-transform duration-300 ease-out"></span>
              </a>

              <a href="{{ route('orders.my') }}"
                class="relative text-sm {{ request()->routeIs('orders.*') ? 'text-purple-900 font-semibold' : 'text-purple-800 hover:text-purple-900' }}">
                My Orders
                <span class="absolute left-0 -bottom-1 h-0.5 w-full origin-left scale-x-{{ request()->routeIs('orders.*') ? '100' : '0' }} bg-gradient-to-r from-purple-600 to-pink-500 transition-transform duration-300 ease-out"></span>
              </a>

              <a href="{{ route('cart.index') }}" class="relative text-sm text-purple-800 hover:text-purple-900">
                <span class="inline-flex items-center gap-2">
                  Cart
                  @if($cartCount > 0)
                    <span class="inline-flex items-center justify-center h-5 min-w-[1.25rem] rounded-full bg-pink-500 text-[10px] text-white px-1.5 shadow">
                      {{ $cartCount }}
                    </span>
                  @endif
                </span>
              </a>

              <a href="{{ route('customer.profile.edit') }}"
                class="relative text-sm {{ request()->routeIs('customer.profile.*') ? 'text-purple-900 font-semibold' : 'text-purple-800 hover:text-purple-900' }}">
                Profile
                <span class="absolute left-0 -bottom-1 h-0.5 w-full origin-left scale-x-{{ request()->routeIs('customer.profile.*') ? '100' : '0' }} bg-gradient-to-r from-purple-600 to-pink-500 transition-transform duration-300 ease-out"></span>
              </a>


              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="rounded-xl px-4 py-2 text-sm font-medium bg-purple-600 text-white shadow hover:bg-purple-700 active:scale-[.99]">
                  Logout
                </button>
              </form>
            </nav>

            {{-- Mobile toggle --}}
            <button id="shopNavToggle" class="md:hidden rounded-lg p-2 text-purple-900 hover:bg-purple-100" aria-label="Toggle menu" aria-expanded="false">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
          </div>
        </div>

        {{-- Mobile drawer --}}
        <div id="shopMobileNav" class="hidden md:hidden border-t border-purple-100 bg-white/95">
          <div class="max-w-7xl mx-auto px-4 py-3 space-y-2 text-sm">
            <a href="{{ route('shop.index') }}" class="block py-2 rounded-lg px-3 hover:bg-purple-50 {{ request()->routeIs('shop.*') ? 'font-semibold text-purple-900' : 'text-purple-800' }}">Shop</a>
            <a href="{{ route('orders.my') }}" class="block py-2 rounded-lg px-3 hover:bg-purple-50 {{ request()->routeIs('orders.*') ? 'font-semibold text-purple-900' : 'text-purple-800' }}">My Orders</a>
            <a href="{{ route('cart.index') }}" class="block py-2 rounded-lg px-3 hover:bg-purple-50 text-purple-800">
              Cart
              @if($cartCount > 0)
                <span class="ml-2 text-xs bg-pink-500 text-white rounded-full px-1.5 py-0.5">{{ $cartCount }}</span>
              @endif
            </a>
            <a href="{{ route('customer.profile.edit') }}"
              class="block py-2 rounded-lg px-3 hover:bg-purple-50 {{ request()->routeIs('customer.profile.*') ? 'font-semibold text-purple-900' : 'text-purple-800' }}">
              Profile
            </a>
            <form action="{{ route('logout') }}" method="POST" class="pt-1">
              @csrf
              <button class="w-full text-left rounded-lg px-3 py-2 bg-purple-600 text-white hover:bg-purple-700">Logout</button>
            </form>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8" id="content">
      @if (session('success'))
        <div class="mb-6 bg-white border border-green-200 text-green-700 rounded-2xl p-4 shadow-sm">{{ session('success') }}</div>
      @endif
      @if ($errors->any())
        <div class="mb-6 bg-white border border-pink-200 text-pink-700 rounded-2xl p-4 shadow-sm">
          <ul class="list-disc ml-5 space-y-1">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
          </ul>
        </div>
      @endif
      @yield('content')
    </main>

    {{-- Modern footer --}}
    <footer class="mt-16 border-t border-purple-100 bg-white/70">
      <div class="relative">
        <div class="pointer-events-none absolute inset-x-0 -top-px h-[2px] bg-gradient-to-r from-purple-600 via-fuchsia-500 to-pink-500/80"></div>

        <div class="max-w-7xl mx-auto px-4 py-12">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-purple-600 to-pink-500 text-white grid place-items-center font-extrabold">B</div>
                <span class="font-extrabold text-purple-900">BeautyBliss</span>
              </div>
              <p class="text-sm text-purple-800/80">Glow with confidence. Curated beauty essentials delivered with care.</p>
            </div>

            <div>
              <h4 class="text-sm font-semibold text-purple-900 mb-3">Shop</h4>
              <ul class="space-y-2 text-sm text-purple-800/90">
                <li><a class="hover:underline" href="{{ route('shop.index') }}">All Products</a></li>
                <li><a class="hover:underline" href="{{ route('orders.my') }}">My Orders</a></li>
                <li><a class="hover:underline" href="{{ route('cart.index') }}">Cart</a></li>
              </ul>
            </div>

            <div>
              <h4 class="text-sm font-semibold text-purple-900 mb-3">Company</h4>
              <ul class="space-y-2 text-sm text-purple-800/90">
                <li><a class="hover:underline" href="{{ url('/about') }}">About</a></li>
                <li><a class="hover:underline" href="{{ url('/contact') }}">Contact</a></li>
                <li><a class="hover:underline" href="{{ url('/careers') }}">Careers</a></li>
              </ul>
            </div>

            <div>
              <h4 class="text-sm font-semibold text-purple-900 mb-3">Stay in the loop</h4>
              <form action="#" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="email" name="subscribe_email" placeholder="Your email"
                      class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
                <button class="rounded-xl bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">Subscribe</button>
              </form>
              <div class="mt-3 flex items-center gap-3 text-purple-800/80">
                <a href="#" aria-label="Instagram" class="hover:text-purple-900">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7Zm5 3.5A5.5 5.5 0 1 1 6.5 13 5.5 5.5 0 0 1 12 7.5Zm0 2A3.5 3.5 0 1 0 15.5 13 3.5 3.5 0 0 0 12 9.5Zm5.75-3a.75.75 0 1 1-.75.75.75.75 0 0 1 .75-.75Z"/></svg>
                </a>
                <a href="#" aria-label="Twitter" class="hover:text-purple-900">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.8c-.7.3-1.5.5-2.2.6.8-.5 1.4-1.2 1.7-2.1-.8.5-1.7.9-2.6 1.1C18.1 4.5 17 4 15.8 4c-2.3 0-4.1 1.9-4.1 4.1 0 .3 0 .6.1.9-3.4-.2-6.5-1.8-8.6-4.2-.4.7-.6 1.4-.6 2.2 0 1.4.7 2.6 1.8 3.3-.6 0-1.2-.2-1.7-.5 0 2 1.4 3.7 3.3 4.1-.3.1-.7.1-1 .1-.2 0-.5 0-.7-.1.5 1.6 2 2.8 3.7 2.8A8.3 8.3 0 0 1 2 18.4c1.7 1.1 3.8 1.8 6 1.8 7.2 0 11.2-6 11.2-11.2v-.5c.8-.5 1.5-1.2 2-2Z"/></svg>
                </a>
                <a href="#" aria-label="YouTube" class="hover:text-purple-900">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3.2 3.2 0 0 0-2.3-2.3C19.1 3.3 12 3.3 12 3.3s-7.1 0-9.2.6A3.2 3.2 0 0 0 .5 6.2C0 8.3 0 12 0 12s0 3.7.5 5.8a3.2 3.2 0 0 0 2.3 2.3C5 20.7 12 20.7 12 20.7s7.1 0 9.2-.6a3.2 3.2 0 0 0 2.3-2.3C24 15.7 24 12 24 12s0-3.7-.5-5.8ZM9.7 15.5v-7L16 12l-6.3 3.5Z"/></svg>
                </a>
              </div>
            </div>
          </div>

          <div class="mt-8 flex flex-col gap-2 sm:flex-row items-center justify-between text-xs text-purple-800/80">
            <p>© {{ date('Y') }} BeautyBliss. All rights reserved.</p>
            <nav class="flex items-center gap-4">
              <a href="{{ url('/terms') }}" class="hover:underline">Terms</a>
              <a href="{{ url('/privacy') }}" class="hover:underline">Privacy</a>
              <a href="{{ url('/shipping') }}" class="hover:underline">Shipping</a>
              <a href="{{ url('/returns') }}" class="hover:underline">Returns</a>
            </nav>
          </div>
        </div>
      </div>
    </footer>

    <script>
      (function () {
        const btn = document.getElementById('shopNavToggle');
        const nav = document.getElementById('shopMobileNav');
        if (!btn || !nav) return;
        const toggle = () => {
          const hidden = nav.classList.toggle('hidden');
          btn.setAttribute('aria-expanded', hidden ? 'false' : 'true');
        };
        btn.addEventListener('click', toggle);
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') nav.classList.add('hidden'); });
        document.addEventListener('click', (e) => {
          if (!nav.contains(e.target) && !btn.contains(e.target) && !nav.classList.contains('hidden')) {
            nav.classList.add('hidden'); btn.setAttribute('aria-expanded','false');
          }
        });
      })();
    </script>
    @endrole
  @else
      {{-- ===================== GUEST (NOT LOGGED IN) ===================== --}}
    <header class="sticky top-0 z-40 bg-white/70 supports-[backdrop-filter]:backdrop-blur border-b border-transparent">
      <div class="relative">
        <div class="pointer-events-none absolute inset-x-0 -bottom-px h-[2px] bg-gradient-to-r from-purple-600 via-fuchsia-500 to-pink-500/80"></div>

        <div class="max-w-7xl mx-auto px-4">
          <div class="flex items-center justify-between py-3">
            {{-- Brand --}}
            <a href="{{ route('shop.index') }}" class="flex items-center gap-2">
              <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-purple-600 to-pink-500 text-white grid place-items-center font-extrabold shadow-sm">B</div>
              <span class="hidden sm:block font-extrabold tracking-wide text-purple-900">BeautyBliss</span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-8">
              <a href="{{ route('shop.index') }}"
                class="relative text-sm {{ request()->routeIs('shop.*') ? 'text-purple-900 font-semibold' : 'text-purple-800 hover:text-purple-900' }}">
                Shop
                <span class="absolute left-0 -bottom-1 h-0.5 w-full origin-left scale-x-{{ request()->routeIs('shop.*') ? '100' : '0' }} bg-gradient-to-r from-purple-600 to-pink-500 transition-transform duration-300"></span>
              </a>

              <a href="{{ route('login') }}" class="text-sm text-purple-800 hover:text-purple-900">Login</a>

              <a href="{{ route('register') }}"
                class="rounded-xl px-4 py-2 text-sm font-semibold bg-gradient-to-r from-purple-600 to-pink-500 text-white shadow hover:from-purple-700 hover:to-pink-600 active:scale-[.99]">
                Sign Up
              </a>
            </nav>

            {{-- Mobile toggle --}}
            <button id="guestNavToggle" class="md:hidden rounded-lg p-2 text-purple-900 hover:bg-purple-100" aria-label="Toggle menu" aria-expanded="false">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
          </div>
        </div>

        {{-- Mobile drawer --}}
        <div id="guestMobileNav" class="hidden md:hidden border-t border-purple-100 bg-white/95">
          <div class="max-w-7xl mx-auto px-4 py-3 space-y-2 text-sm">
            <a href="{{ route('shop.index') }}" class="block py-2 rounded-lg px-3 hover:bg-purple-50 {{ request()->routeIs('shop.*') ? 'font-semibold text-purple-900' : 'text-purple-800' }}">Shop</a>
            <a href="{{ route('login') }}" class="block py-2 rounded-lg px-3 hover:bg-purple-50 text-purple-800">Login</a>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white shadow hover:from-purple-700 hover:to-pink-600">
              Sign Up
            </a>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8" id="content">
      @yield('content')
    </main>

    {{-- Modern footer (shared style) --}}
    <footer class="mt-16 border-t border-purple-100 bg-white/70">
      <div class="relative">
        <div class="pointer-events-none absolute inset-x-0 -top-px h-[2px] bg-gradient-to-r from-purple-600 via-fuchsia-500 to-pink-500/80"></div>

        <div class="max-w-7xl mx-auto px-4 py-12">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-purple-600 to-pink-500 text-white grid place-items-center font-extrabold">B</div>
                <span class="font-extrabold text-purple-900">BeautyBliss</span>
              </div>
              <p class="text-sm text-purple-800/80">Discover products loved by the community and tailored to your routine.</p>
            </div>

            <div>
              <h4 class="text-sm font-semibold text-purple-900 mb-3">Explore</h4>
              <ul class="space-y-2 text-sm text-purple-800/90">
                <li><a class="hover:underline" href="{{ route('shop.index') }}">Shop</a></li>
                <li><a class="hover:underline" href="{{ route('login') }}">Login</a></li>
                <li><a class="hover:underline" href="{{ route('register') }}">Sign Up</a></li>
              </ul>
            </div>

            <div>
              <h4 class="text-sm font-semibold text-purple-900 mb-3">Help</h4>
              <ul class="space-y-2 text-sm text-purple-800/90">
                <li><a class="hover:underline" href="{{ url('/support') }}">Support</a></li>
                <li><a class="hover:underline" href="{{ url('/shipping') }}">Shipping</a></li>
                <li><a class="hover:underline" href="{{ url('/returns') }}">Returns</a></li>
              </ul>
            </div>

            <div>
              <h4 class="text-sm font-semibold text-purple-900 mb-3">Newsletter</h4>
              <form action="#" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="email" name="subscribe_email" placeholder="Your email"
                      class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
                <button class="rounded-xl bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">Subscribe</button>
              </form>
              <p class="mt-2 text-xs text-purple-800/70">We’ll only send the good stuff. No spam.</p>
            </div>
          </div>

          <div class="mt-8 flex flex-col gap-2 sm:flex-row items-center justify-between text-xs text-purple-800/80">
            <p>© {{ date('Y') }} BeautyBliss. All rights reserved.</p>
            <nav class="flex items-center gap-4">
              <a href="{{ url('/terms') }}" class="hover:underline">Terms</a>
              <a href="{{ url('/privacy') }}" class="hover:underline">Privacy</a>
              <a href="{{ url('/contact') }}" class="hover:underline">Contact</a>
            </nav>
          </div>
        </div>
      </div>
    </footer>

    <script>
      (function () {
        const btn = document.getElementById('guestNavToggle');
        const nav = document.getElementById('guestMobileNav');
        if (!btn || !nav) return;
        const toggle = () => {
          const hidden = nav.classList.toggle('hidden');
          btn.setAttribute('aria-expanded', hidden ? 'false' : 'true');
        };
        btn.addEventListener('click', toggle);
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') nav.classList.add('hidden'); });
        document.addEventListener('click', (e) => {
          if (!nav.contains(e.target) && !btn.contains(e.target) && !nav.classList.contains('hidden')) {
            nav.classList.add('hidden'); btn.setAttribute('aria-expanded','false');
          }
        });
      })();
    </script>
  @endauth
@stack('scripts')
</body>
</html>
