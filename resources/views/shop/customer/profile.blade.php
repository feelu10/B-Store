@extends('layouts.app')
{{-- Your big layout already shows header/footer when @auth non-admin --}}

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="mb-6">
    <h1 class="text-2xl font-extrabold text-purple-900">My Profile</h1>
    <p class="text-sm text-purple-800/80">Save your shipping details for faster checkout.</p>
  </div>

  <form method="POST" action="{{ route('customer.profile.update') }}"
        class="bg-white border border-purple-100 rounded-2xl p-5 shadow-sm space-y-8"
        x-data>
    @csrf
    @method('PATCH')

    {{-- Basic info --}}
    <section>
      <h2 class="text-sm font-semibold text-purple-900 mb-3">Basic Information</h2>
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">First Name</label>
          <input type="text" name="first_name" value="{{ old('first_name', $profile->first_name) }}" placeholder="Jane"
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>
        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">Last Name</label>
          <input type="text" name="last_name" value="{{ old('last_name', $profile->last_name) }}" placeholder="Doe"
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>

        {{-- Phone (US, normalized to +1XXXXXXXXXX) --}}
        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-purple-800 mb-1">Phone</label>
          <input
            type="tel"
            name="phone"
            inputmode="tel"
            autocomplete="tel"
            maxlength="12" {{-- +1 + 10 digits --}}
            value="{{ old('phone', $profile->phone) }}"
            placeholder="+1 555 123 4567"
            pattern="^(?:\+1)?\d{10}$"
            title="Enter a valid US phone: 10 digits, e.g. +15551234567 or 5551234567"
            class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
            x-on:input="
              // keep only + and digits
              let v = $el.value.replace(/[^+\d]/g,'');
              // strip all non-digits except leading +
              let digits = v.replace(/\D/g,'');
              // remove leading 1s beyond the first country code handling
              if (digits.length > 11) digits = digits.slice(0,11);
              // if user typed 11 digits starting with 1, treat as country code + 10
              if (digits.length === 11 && digits.startsWith('1')) {
                v = '+1' + digits.slice(1, 11);
              } else {
                // ensure 10-digit NANP; when between 1..10 digits, don't force +1 yet
                if (digits.length <= 10) {
                  v = digits;
                } else {
                  // 11 digits but not starting with 1 → clamp to 10
                  v = digits.slice(0,10);
                }
              }
              // when exactly 10 digits, present as +1XXXXXXXXXX
              if (v.length === 10 && !v.startsWith('+1')) v = '+1' + v;
              $el.value = v;
            "
          >
          <p class="mt-1 text-[11px] text-purple-800/70">
            Accepts <code class="font-mono">5551234567</code> or <code class="font-mono">+15551234567</code>. We normalize to <code class="font-mono">+1XXXXXXXXXX</code>.
          </p>
        </div>
      </div>
    </section>

    {{-- Shipping (US) --}}
    <section>
      <h2 class="text-sm font-semibold text-purple-900 mb-3">Shipping Address</h2>
      <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-purple-800 mb-1">Full Name (Recipient)</label>
          <input type="text" name="ship_full_name"
                 value="{{ old('ship_full_name', $profile->ship_full_name ?? ($profile->first_name || $profile->last_name ? trim(($profile->first_name ?? '').' '.($profile->last_name ?? '')) : '')) }}"
                 placeholder="Jane Doe" required
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-purple-800 mb-1">Address Line 1</label>
          <input type="text" name="ship_line1" value="{{ old('ship_line1', $profile->ship_line1) }}" placeholder="1234 Main St" required
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-purple-800 mb-1">Address Line 2 (Optional)</label>
          <input type="text" name="ship_line2" value="{{ old('ship_line2', $profile->ship_line2) }}" placeholder="Apartment, suite, unit, building, floor, etc."
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>

        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">City</label>
          <input type="text" name="ship_city" value="{{ old('ship_city', $profile->ship_city) }}" placeholder="City" required
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>

        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">State</label>
          <select name="ship_state" required
                  class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
            @php
              $states = [
                'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia',
                'HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine',
                'MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada',
                'NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon',
                'PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia',
                'WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming','DC'=>'District of Columbia'
              ];
              $shipState = old('ship_state', $profile->ship_state ?? '');
            @endphp
            <option value="" disabled {{ $shipState==='' ? 'selected' : '' }}>Select state</option>
            @foreach($states as $abbr => $name)
              <option value="{{ $abbr }}" {{ $shipState===$abbr ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>

        {{-- ZIP: 5 or ZIP+4 --}}
        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">ZIP Code</label>
          <input type="text"
                 name="ship_postal_code"
                 inputmode="numeric"
                 maxlength="10" {{-- 12345-6789 --}}
                 pattern="^\d{5}(?:-\d{4})?$"
                 title="Enter a valid 5-digit ZIP or ZIP+4 (12345 or 12345-6789)"
                 value="{{ old('ship_postal_code', $profile->ship_postal_code) }}"
                 placeholder="e.g., 90210 or 90210-1234"
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                 x-on:input="
                   let v = $el.value.toUpperCase().replace(/[^0-9-]/g,'');
                   // auto insert dash after 5 if user keeps typing
                   v = v.replace(/^(\d{5})(\d{1,4}).*$/, '$1-$2');
                   // clamp to 10 including dash
                   $el.value = v.slice(0,10);
                 "
                 required>
        </div>

        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">Country</label>
          <input type="text" name="ship_country" value="{{ old('ship_country', $profile->ship_country ?? 'United States') }}" placeholder="United States" required
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-purple-800 mb-1">Delivery Notes (Optional)</label>
          <input type="text" name="ship_landmark" value="{{ old('ship_landmark', $profile->ship_landmark) }}" placeholder="Gate code, leave at front desk, etc."
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>

        <label class="sm:col-span-2 inline-flex items-center gap-2 text-sm">
          <input type="checkbox" name="ship_is_default" value="1" {{ old('ship_is_default', $profile->ship_is_default) ? 'checked' : '' }}
                 class="h-4 w-4 rounded border-purple-300 text-purple-600 focus:ring-purple-500">
          <span>Set as my default shipping address</span>
        </label>
      </div>
    </section>

    {{-- Billing (US) --}}
    <section x-data="{ separate: {{ old('has_separate_billing', $profile->has_separate_billing) ? 'true':'false' }} }">
      <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-purple-900">Billing Address</h2>
        <label class="inline-flex items-center gap-2 text-sm">
          <input type="checkbox" name="has_separate_billing" x-model="separate" value="1"
                 {{ old('has_separate_billing', $profile->has_separate_billing) ? 'checked' : '' }}
                 class="h-4 w-4 rounded border-purple-300 text-purple-600 focus:ring-purple-500">
          <span>Use a different billing address</span>
        </label>
      </div>

      <div class="mt-3 grid sm:grid-cols-2 gap-4" x-show="separate" x-cloak>
        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-purple-800 mb-1">Full Name</label>
          <input type="text" name="bill_full_name" value="{{ old('bill_full_name', $profile->bill_full_name) }}" placeholder="Billing Name"
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-purple-800 mb-1">Address Line 1</label>
          <input type="text" name="bill_line1" value="{{ old('bill_line1', $profile->bill_line1) }}" placeholder="1234 Main St"
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-purple-800 mb-1">Address Line 2 (Optional)</label>
          <input type="text" name="bill_line2" value="{{ old('bill_line2', $profile->bill_line2) }}" placeholder="Apartment, suite, unit…"
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>

        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">City</label>
          <input type="text" name="bill_city" value="{{ old('bill_city', $profile->bill_city) }}" placeholder="City"
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>

        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">State</label>
          @php $billState = old('bill_state', $profile->bill_state ?? ''); @endphp
          <select name="bill_state"
                  class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
            <option value="" disabled {{ $billState==='' ? 'selected' : '' }}>Select state</option>
            @foreach($states as $abbr => $name)
              <option value="{{ $abbr }}" {{ $billState===$abbr ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">ZIP Code</label>
          <input type="text"
                 name="bill_postal_code"
                 inputmode="numeric"
                 maxlength="10"
                 pattern="^\d{5}(?:-\d{4})?$"
                 title="Enter a valid 5-digit ZIP or ZIP+4 (12345 or 12345-6789)"
                 value="{{ old('bill_postal_code', $profile->bill_postal_code) }}"
                 placeholder="e.g., 90210 or 90210-1234"
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                 x-on:input="
                   let v = $el.value.toUpperCase().replace(/[^0-9-]/g,'');
                   v = v.replace(/^(\d{5})(\d{1,4}).*$/, '$1-$2');
                   $el.value = v.slice(0,10);
                 ">
        </div>

        <div>
          <label class="block text-xs font-medium text-purple-800 mb-1">Country</label>
          <input type="text" name="bill_country" value="{{ old('bill_country', $profile->bill_country ?? 'United States') }}" placeholder="United States"
                 class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm placeholder-purple-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
        </div>
      </div>
    </section>

    <div class="pt-2 flex items-center gap-3">
      <a href="{{ route('orders.my') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-purple-800 bg-purple-50 hover:bg-purple-100 border border-purple-200">Back to Orders</a>
      <button class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 shadow active:scale-[.99]">Save Profile</button>
    </div>
  </form>
</div>

{{-- Alpine for the billing toggle and input guards --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
