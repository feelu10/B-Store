<x-guest-layout>
  {{-- Header --}}
  <div class="text-center mb-6">
    <div class="mx-auto mb-4 h-12 w-12 grid place-items-center rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 text-white shadow-lg shadow-purple-500/30">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 11.5v4m0-8v.5M4 7l8-4 8 4v10l-8 4-8-4V7Z"/>
      </svg>
    </div>
    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-gray-900">Forgot your password?</h1>
    <p class="mt-1 text-sm text-gray-600">
      Enter your email and we’ll send you a link to reset it.
    </p>
  </div>

  {{-- Accent line --}}
  <div class="h-1.5 -mt-2 mb-5 rounded-full bg-gradient-to-r from-purple-600 via-fuchsia-500 to-pink-500"></div>

  {{-- Session status (success message after submitting) --}}
  <x-auth-session-status class="mb-4" :status="session('status')" />

  <form method="POST" action="{{ route('password.email') }}" class="space-y-5" novalidate>
    @csrf

    {{-- Email --}}
    <div>
      <x-input-label for="email" :value="__('Email address')" />
      <div class="relative mt-1">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20.25 6H3.75A1.75 1.75 0 0 0 2 7.75v8.5A1.75 1.75 0 0 0 3.75 18h16.5A1.75 1.75 0 0 0 22 16.25v-8.5A1.75 1.75 0 0 0 20.25 6Zm-.45 2L12 12.59 4.2 8h15.6Z"/>
          </svg>
        </span>
        <x-text-input
          id="email"
          name="email"
          type="email"
          :value="old('email')"
          required
          autofocus
          autocomplete="email"
          class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
        />
      </div>
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
      <p class="mt-2 text-xs text-gray-500">We’ll send a secure link that expires after a short time.</p>
    </div>

    {{-- Actions --}}
    <div class="space-y-3">
      <x-primary-button class="w-full justify-center rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-lg shadow-purple-500/30">
        {{ __('Email Password Reset Link') }}
      </x-primary-button>

      <div class="flex items-center justify-between text-sm">
        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 underline underline-offset-4">
          Back to sign in
        </a>
        <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900 underline underline-offset-4">
          Create a new account
        </a>
      </div>
    </div>
  </form>
</x-guest-layout>
