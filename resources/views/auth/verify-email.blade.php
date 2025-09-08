<x-guest-layout>
  {{-- status alert (after resend) --}}
  @if (session('status') === 'verification-link-sent')
    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
      A new verification link has been sent to your email.
    </div>
  @endif

  {{-- header --}}
  <div class="text-center mb-7">
    <div class="mx-auto mb-4 h-12 w-12 grid place-items-center rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 text-white shadow-lg shadow-purple-500/30">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 8l9 6 9-6M5 19h14a2 2 0 0 0 2-2V7"/>
      </svg>
    </div>
    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-gray-900">Verify your email</h1>
    <p class="mt-1 text-sm text-gray-600">
      We sent a verification link to your inbox. Click it to activate your account.
    </p>
  </div>

  {{-- tips --}}
  <ul class="mb-6 space-y-2 text-sm text-gray-600">
    <li>• Didn’t get it? Check spam or promotions folders.</li>
    <li>• Make sure <span class="font-medium text-gray-900">you entered the correct email</span> at sign up.</li>
  </ul>

  {{-- actions --}}
  <div class="space-y-3">
    {{-- resend link --}}
    <form method="POST" action="{{ route('verification.send') }}" x-data @submit.prevent="$el.submit(); $refs.resendBtn.disabled = true;">
      @csrf
      <button type="submit" x-ref="resendBtn"
              class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 px-4 py-2.5 font-semibold text-white shadow-lg shadow-purple-500/30 transition hover:from-purple-700 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-purple-300">
        Resend verification email
      </button>
    </form>

    {{-- open email app (optional helper) --}}
    <a href="mailto:" class="block w-full text-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
      Open your email app
    </a>

    {{-- back to login --}}
    <a href="{{ route('login') }}" class="block w-full text-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
      Back to sign in
    </a>

    {{-- log out (if the user is authenticated viewing this page) --}}
    @auth
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="mt-1 w-full text-center text-sm text-gray-500 hover:text-gray-800 underline">
          Wrong email? Log out and try a different account
        </button>
      </form>
    @endauth
  </div>

  {{-- subtle footer --}}
  <p class="mt-8 text-center text-xs text-gray-500">
    Need help? <a href="{{ url('/support') }}" class="underline hover:text-gray-800">Contact support</a>
  </p>
</x-guest-layout>
