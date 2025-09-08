<x-guest-layout>
  {{-- Header --}}
  <div class="text-center mb-6">
    <div class="mx-auto mb-4 h-12 w-12 grid place-items-center rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 text-white shadow-lg shadow-purple-500/30">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 11.5v4m0-8v.5M4 7l8-4 8 4v10l-8 4-8-4V7Z"/>
      </svg>
    </div>
    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-gray-900">Create a new password</h1>
    <p class="mt-1 text-sm text-gray-600">Choose a strong password to secure your account.</p>
  </div>

  {{-- Accent line --}}
  <div class="h-1.5 -mt-2 mb-5 rounded-full bg-gradient-to-r from-purple-600 via-fuchsia-500 to-pink-500"></div>

  <form method="POST" action="{{ route('password.store') }}" class="space-y-5" novalidate>
    @csrf

    {{-- Password Reset Token --}}
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    {{-- Email --}}
    <div>
      <x-input-label for="email" :value="__('Email address')" />
      <div class="relative mt-1">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.25 6H3.75A1.75 1.75 0 0 0 2 7.75v8.5A1.75 1.75 0 0 0 3.75 18h16.5A1.75 1.75 0 0 0 22 16.25v-8.5A1.75 1.75 0 0 0 20.25 6Zm-.45 2L12 12.59 4.2 8h15.6Z"/></svg>
        </span>
        <x-text-input
          id="email"
          class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
          type="email"
          name="email"
          :value="old('email', $request->email)"
          required
          autofocus
          autocomplete="username"
          readonly
        />
      </div>
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    {{-- Password --}}
    <div>
      <x-input-label for="password" :value="__('New password')" />
      <div class="relative mt-1">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2ZM9 7a3 3 0 1 1 6 0v2H9Z"/></svg>
        </span>
        <x-text-input
          id="password"
          class="block w-full pl-10 pr-10 py-2.5 rounded-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
          type="password"
          name="password"
          required
          autocomplete="new-password"
        />
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

      <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    {{-- Confirm Password --}}
    <div>
      <x-input-label for="password_confirmation" :value="__('Confirm new password')" />
      <div class="relative mt-1">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2ZM9 7a3 3 0 1 1 6 0v2H9Z"/></svg>
        </span>
        <x-text-input
          id="password_confirmation"
          class="block w-full pl-10 pr-10 py-2.5 rounded-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
          type="password"
          name="password_confirmation"
          required
          autocomplete="new-password"
        />
        <button type="button" id="togglePwd2"
                class="absolute inset-y-0 right-2.5 my-auto h-8 w-8 grid place-items-center rounded-full text-gray-400 hover:bg-gray-100"
                aria-label="Show confirm password">
          <svg id="eyeOpen2" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11.2a4.2 4.2 0 1 1 0-8.4 4.2 4.2 0 0 1 0 8.4Z"/></svg>
          <svg id="eyeClosed2" xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="m3.27 1.72-1.4 1.41 3.2 3.2C3.2 7.56 1.77 9.1 1 10.95c1.73 3.89 6 7 11 7 1.59 0 3.1-.28 4.49-.79l3.38 3.38 1.41-1.41L3.27 1.72ZM12 15.6a3.6 3.6 0 0 1-3.31-5.09l4.8 4.8c-.45.19-.95.29-1.49.29Z"/></svg>
        </button>
      </div>
      <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    {{-- Actions --}}
    <div class="space-y-3">
      <x-primary-button class="w-full justify-center rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-lg shadow-purple-500/30">
        {{ __('Reset Password') }}
      </x-primary-button>

      <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline underline-offset-4">
          Back to sign in
        </a>
      </div>
    </div>
  </form>

  @push('scripts')
  <script>
    (() => {
      const pwd = document.getElementById('password');
      const pwd2 = document.getElementById('password_confirmation');

      // Toggle helpers
      const setupToggle = (inputId, btnId, openId, closedId) => {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(btnId);
        const open = document.getElementById(openId);
        const closed = document.getElementById(closedId);
        btn?.addEventListener('click', () => {
          const show = input.type === 'password';
          input.type = show ? 'text' : 'password';
          open.classList.toggle('hidden', !show);
          closed.classList.toggle('hidden', show);
          btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
      };
      setupToggle('password','togglePwd','eyeOpen','eyeClosed');
      setupToggle('password_confirmation','togglePwd2','eyeOpen2','eyeClosed2');

      // Strength meter
      const fill = document.getElementById('pwdFill');
      const hint = document.getElementById('pwdHint');
      const colors = ['bg-red-500','bg-orange-500','bg-yellow-500','bg-lime-500','bg-green-500'];
      const score = (v) => {
        let s = 0;
        if (!v) return 0;
        if (v.length >= 8) s++;
        if (/[A-Z]/.test(v)) s++;
        if (/[a-z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v)) s++;
        return Math.min(s, 5);
      };
      pwd?.addEventListener('input', () => {
        const s = score(pwd.value);
        fill.style.width = (s / 5 * 100) + '%';
        colors.forEach(c => fill.classList.remove(c));
        if (s > 0) fill.classList.add(colors[s-1]);
        if (hint) hint.textContent = s < 3 ? 'Use 8+ chars with letters, numbers & symbols.' : 'Strong password ✔';
      });
    })();
  </script>
  @endpush
</x-guest-layout>
