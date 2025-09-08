<x-guest-layout>
  {{-- Header --}}
  <div class="mb-7 text-center">
    <div class="mx-auto mb-4 h-12 w-12 grid place-items-center rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 text-white shadow-lg shadow-purple-500/30">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M11.25 4.5l.66 2.63a2.25 2.25 0 002.19 1.72h2.59l-2.1 1.52a2.25 2.25 0 00-.84 2.52l.9 2.76-2.31-1.61a2.25 2.25 0 00-2.58 0L6.75 18l.9-2.76a2.25 2.25 0 00-.84-2.52L4.71 10.5h2.59a2.25 2.25 0 002.19-1.72l.66-2.28z"/>
      </svg>
    </div>
    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-gray-900">Create your account</h1>
    <p class="mt-1 text-sm text-gray-500">Join and get access to curated essentials & perks.</p>
  </div>

  {{-- Card accent --}}
  <div class="h-1.5 -mt-2 mb-5 rounded-full bg-gradient-to-r from-purple-600 via-fuchsia-500 to-pink-500"></div>

  {{-- Error summary --}}
  @if ($errors->any())
    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
      <strong class="font-semibold">Please review the highlighted fields.</strong>
    </div>
  @endif

  <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
    @csrf

    {{-- Name --}}
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
      <div class="relative mt-1">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a4.5 4.5 0 1 0-4.5-4.5A4.505 4.505 0 0 0 12 12Zm0 1.5c-3.318 0-9 1.665-9 4.5V21h18v-3c0-2.835-5.682-4.5-9-4.5Z"/></svg>
        </span>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required
               autocomplete="name"
               class="w-full rounded-xl border border-gray-300 bg-white pl-10 pr-3 py-2.5 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-200" />
      </div>
      @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div>
      <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
      <div class="relative mt-1">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.25 6H3.75A1.75 1.75 0 0 0 2 7.75v8.5A1.75 1.75 0 0 0 3.75 18h16.5A1.75 1.75 0 0 0 22 16.25v-8.5A1.75 1.75 0 0 0 20.25 6Zm-.45 2L12 12.59 4.2 8h15.6Z"/></svg>
        </span>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required
               autocomplete="username"
               class="w-full rounded-xl border border-gray-300 bg-white pl-10 pr-3 py-2.5 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-200" />
      </div>
      @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Password --}}
    <div>
      <div class="flex items-center justify-between">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
      </div>
      <div class="relative mt-1">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2ZM9 7a3 3 0 1 1 6 0v2H9Z"/></svg>
        </span>
        <input id="password" name="password" type="password" required
               autocomplete="new-password"
               class="w-full rounded-xl border border-gray-300 bg-white pl-10 pr-10 py-2.5 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-200" />
        <button type="button" id="togglePwd"
                class="absolute inset-y-0 right-2.5 my-auto h-8 w-8 grid place-items-center rounded-full text-gray-400 hover:bg-gray-100"
                aria-label="Show password">
          <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11.2a4.2 4.2 0 1 1 0-8.4 4.2 4.2 0 0 1 0 8.4Z"/></svg>
          <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="m3.27 1.72-1.4 1.41 3.2 3.2C3.2 7.56 1.77 9.1 1 10.95c1.73 3.89 6 7 11 7 1.59 0 3.1-.28 4.49-.79l3.38 3.38 1.41-1.41L3.27 1.72ZM12 15.6a3.6 3.6 0 0 1-3.31-5.09l4.8 4.8c-.45.19-.95.29-1.49.29Z"/></svg>
        </button>
      </div>
      {{-- Strength meter --}}
      <div class="mt-2">
        <div class="h-1.5 w-full rounded-full bg-gray-100 overflow-hidden">
          <div id="pwdFill" class="h-full w-0 bg-red-500 transition-all duration-300"></div>
        </div>
        <p id="pwdHint" class="mt-1 text-xs text-gray-500">Use 8+ chars with letters, numbers & symbols.</p>
      </div>
      @error('password') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Confirm Password --}}
    <div>
      <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
      <div class="relative mt-1">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2ZM9 7a3 3 0 1 1 6 0v2H9Z"/></svg>
        </span>
        <input id="password_confirmation" name="password_confirmation" type="password" required
               autocomplete="new-password"
               class="w-full rounded-xl border border-gray-300 bg-white pl-10 pr-3 py-2.5 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-200" />
      </div>
    </div>

    {{-- TOS --}}
    <div class="flex items-start gap-3">
      <input id="terms" name="terms" type="checkbox" required
             class="mt-1.5 h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
      <label for="terms" class="text-sm text-gray-600">
        I agree to the <a href="{{ url('/terms') }}" class="underline hover:text-gray-900">Terms</a> and
        <a href="{{ url('/privacy') }}" class="underline hover:text-gray-900">Privacy Policy</a>.
      </label>
    </div>

    {{-- Submit --}}
    <button type="submit"
            class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 px-4 py-2.5 font-semibold text-white shadow-lg shadow-purple-500/30 transition hover:from-purple-700 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-purple-300 active:scale-[.99]">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-90 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M16.5 12 21 7.5M21 7.5 16.5 3M21 7.5H9.75A6.75 6.75 0 0 0 3 14.25V21"/>
      </svg>
      Create account
    </button>

    {{-- Sub-footer --}}
    <p class="text-center text-sm text-gray-600">
      Already have an account?
      <a href="{{ route('login') }}" class="font-medium text-gray-900 underline decoration-purple-400 underline-offset-4 hover:decoration-purple-600">Sign in</a>
    </p>
   
  </form>

  {{-- Minimal JS: toggle + strength --}}
  @push('scripts')
  <script>
    (() => {
      const pwd = document.getElementById('password');
      const toggle = document.getElementById('togglePwd');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');
      const fill = document.getElementById('pwdFill');
      const hint = document.getElementById('pwdHint');

      // Toggle
      toggle?.addEventListener('click', () => {
        const show = pwd.type === 'password';
        pwd.type = show ? 'text' : 'password';
        eyeOpen.classList.toggle('hidden', !show);
        eyeClosed.classList.toggle('hidden', show);
        toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      });

      // Strength (simple heuristic)
      const score = v => {
        let s = 0;
        if (!v) return 0;
        if (v.length >= 8) s++;
        if (/[A-Z]/.test(v)) s++;
        if (/[a-z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v)) s++;
        return Math.min(s, 5);
      };
      const colors = ['bg-red-500','bg-orange-500','bg-yellow-500','bg-lime-500','bg-green-500'];

      pwd?.addEventListener('input', () => {
        const s = score(pwd.value);
        fill.style.width = (s / 5 * 100) + '%';
        colors.forEach(c => fill.classList.remove(c));
        if (s > 0) fill.classList.add(colors[s-1]);
        hint && (hint.textContent = s < 3 ? 'Use 8+ chars with letters, numbers & symbols.' : 'Strong password ✔');
      });
    })();
  </script>
  @endpush
</x-guest-layout>
