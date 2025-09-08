<!DOCTYPE html>
<html lang="en" class="h-full antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in</title>
  {{-- Tailwind (use @vite in production if you prefer) --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              50:  '#faf5ff',
              100: '#f3e8ff',
              200: '#e9d5ff',
              300: '#d8b4fe',
              400: '#c084fc',
              500: '#a855f7',
              600: '#9333ea',
              700: '#7e22ce',
              800: '#6b21a8',
              900: '#581c87'
            }
          }
        }
      }
    }
  </script>
</head>
<body class="h-full bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-900 dark:via-slate-950 dark:to-black">
  <div class="min-h-screen grid place-items-center px-4 py-10">
    <!-- Card -->
    <div class="w-full max-w-md">
      <div class="mb-8 text-center">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">⚡</span>
          <span class="text-2xl font-extrabold tracking-tight text-slate-800 dark:text-slate-100">YourBrand</span>
        </a>
      </div>

      <div class="relative">
        <div class="absolute inset-0 rounded-3xl bg-gradient-to-tr from-brand-500/20 via-transparent to-indigo-500/10 blur-2xl"></div>
        <div class="relative rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur-sm shadow-xl dark:bg-slate-900/60 dark:border-slate-800">
          <div class="p-6 sm:p-8">
            <div class="mb-6">
              <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Welcome back</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sign in to your account</p>
            </div>

            {{-- Session status --}}
            @if (session('status'))
              <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700 dark:border-green-900/40 dark:bg-green-900/30 dark:text-green-200">
                {{ session('status') }}
              </div>
            @endif

            {{-- Global errors (optional) --}}
            @if ($errors->any())
              <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700 dark:border-rose-900/40 dark:bg-rose-900/30 dark:text-rose-200">
                {{ __('There were some problems with your input.') }}
              </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
              @csrf

              <!-- Email -->
              <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                  Email
                </label>
                <div class="relative">
                  <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                    autofocus
                    class="peer block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-400 outline-none ring-0 transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:border-brand-500 dark:focus:ring-brand-900/40"
                    placeholder="you@example.com"
                  />
                  <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400 peer-focus:text-brand-500">
                    <!-- envelope icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 7.5v9A2.25 2.25 0 0 1 19.5 18.75h-15A2.25 2.25 0 0 1 2.25 16.5v-9m19.5 0A2.25 2.25 0 0 0 19.5 5.25h-15A2.25 2.25 0 0 0 2.25 7.5m19.5 0-9.75 6.75L2.25 7.5"/>
                    </svg>
                  </div>
                </div>
                @error('email')
                  <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
              </div>

              <!-- Password -->
              <div>
                <div class="flex items-center justify-between">
                  <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Password
                  </label>
                  @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-700 hover:underline dark:text-brand-300">
                      Forgot?
                    </a>
                  @endif
                </div>
                <div class="relative">
                  <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="peer block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-slate-900 placeholder-slate-400 outline-none ring-0 transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:border-brand-500 dark:focus:ring-brand-900/40"
                    placeholder="••••••••"
                  />
                  <button type="button" id="togglePassword" aria-label="Toggle password visibility"
                    class="absolute inset-y-0 right-2 inline-flex items-center rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-300 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <!-- eye icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.576 3.01 9.964 7.178.07.213.07.431 0 .644C20.576 16.49 16.64 19.5 12 19.5c-4.64 0-8.577-3.01-9.964-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <!-- eye-off icon (hidden by default) -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeClosed" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3.98 8.223 1.713 1.713M20.02 15.777l-1.713-1.713M9.878 9.878a3 3 0 0 0 4.243 4.243M6.708 6.708A9.707 9.707 0 0 1 12 4.5c4.64 0 8.576 3.01 9.964 7.178.07.213.07.431 0 .644a10.649 10.649 0 0 1-3.22 4.63M6.708 6.708 3 3m0 0 18 18" />
                    </svg>
                  </button>
                </div>
                @error('password')
                  <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
              </div>

              <!-- Remember me -->
              <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2">
                  <input id="remember_me" name="remember" type="checkbox"
                         class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-700">
                  <span class="text-sm text-slate-600 dark:text-slate-300">Remember me</span>
                </label>
                @if (Route::has('register'))
                  <a href="{{ route('register') }}" class="text-sm text-slate-500 hover:text-slate-700 hover:underline dark:text-slate-400 dark:hover:text-slate-200">
                    Create account
                  </a>
                @endif
              </div>

              <!-- Submit -->
              <button type="submit"
                class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 font-semibold text-white shadow-lg shadow-brand-600/30 transition hover:bg-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-300 active:scale-[.99]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-90 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 12 21 7.5m0 0L16.5 3M21 7.5H9.75a6.75 6.75 0 0 0-6.75 6.75V21" />
                </svg>
                Sign in
              </button>
            </form>

            <!-- Divider -->
            
          </div>

          <div class="rounded-b-3xl border-t border-slate-200/80 bg-slate-50/70 p-4 text-center text-xs text-slate-500 dark:border-slate-800 dark:bg-slate-900/70 dark:text-slate-400">
            By continuing you agree to our <a href="{{ url('/terms') }}" class="underline hover:text-slate-700 dark:hover:text-slate-200">Terms</a> &amp; <a href="{{ url('/privacy') }}" class="underline hover:text-slate-700 dark:hover:text-slate-200">Privacy</a>.
          </div>
        </div>
      </div>

      <!-- Theme toggle (optional) -->
      <div class="mt-6 flex justify-center">
        <button type="button" id="themeToggle"
          class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
          <span>Toggle theme</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M21.64 13a1 1 0 0 0-1.05-.14 8 8 0 1 1-9.45-9.45A1 1 0 0 0 12 2a10 10 0 1 0 9.64 11Z"/>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <script>
    // Password visibility
    (function () {
      const input = document.getElementById('password');
      const btn = document.getElementById('togglePassword');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');
      if (!input || !btn) return;
      btn.addEventListener('click', () => {
        const isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        eyeOpen.classList.toggle('hidden', !isPwd);
        eyeClosed.classList.toggle('hidden', isPwd);
        input.focus();
      });
    })();

    // Simple theme toggle (remember in localStorage)
    (function () {
      const key = 'theme';
      const toggle = document.getElementById('themeToggle');
      const root = document.documentElement;
      const apply = (v) => v === 'dark' ? root.classList.add('dark') : root.classList.remove('dark');
      apply(localStorage.getItem(key) || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
      toggle?.addEventListener('click', () => {
        const next = root.classList.contains('dark') ? 'light' : 'dark';
        localStorage.setItem(key, next);
        apply(next);
      });
    })();
  </script>
</body>
</html>
