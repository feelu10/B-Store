<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = Auth::user();

        // Seed first/last from users.name if available
        $seedFirst = null;
        $seedLast  = null;
        if (!empty($user->name)) {
            $parts = preg_split('/\s+/', trim($user->name), 2);
            $seedFirst = $parts[0] ?? null;
            $seedLast  = $parts[1] ?? null;
        }

        $profile = CustomerProfile::firstOrCreate(['user_id' => $user->id], [
            'first_name' => $seedFirst,
            'last_name'  => $seedLast,
            'ship_country' => 'United States',
            'bill_country' => 'United States',
        ]);

        $title = 'My Profile';
        return view('shop.customer.profile', compact('title', 'user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = CustomerProfile::firstOrCreate(['user_id' => $user->id]);

        $validated = $request->validate([
            // basic
            'first_name' => ['nullable','string','max:120'],
            'last_name'  => ['nullable','string','max:120'],

            // US phone: 10 digits or +1 + 10 digits
            'phone'      => ['required','regex:/^(?:\+1)?\d{10}$/'],

            // shipping (US)
            'ship_full_name'   => ['required','string','max:160'],
            'ship_line1'       => ['required','string','max:255'],
            'ship_line2'       => ['nullable','string','max:255'],
            'ship_city'        => ['required','string','max:160'],
            'ship_state'       => ['required','string','size:2'],              // e.g., CA, NY
            'ship_postal_code' => ['required','regex:/^\d{5}(?:-\d{4})?$/'],   // 12345 or 12345-6789
            'ship_country'     => ['required','string','max:120'],
            'ship_landmark'    => ['nullable','string','max:255'],
            'ship_is_default'  => ['nullable','boolean'],

            // billing (US)
            'has_separate_billing' => ['nullable','boolean'],
            'bill_full_name'   => ['nullable','string','max:160'],
            'bill_line1'       => ['nullable','string','max:255'],
            'bill_line2'       => ['nullable','string','max:255'],
            'bill_city'        => ['nullable','string','max:160'],
            'bill_state'       => ['nullable','string','size:2'],
            'bill_postal_code' => ['nullable','regex:/^\d{5}(?:-\d{4})?$/'],
            'bill_country'     => ['nullable','string','max:120'],
        ]);

        // normalize checkboxes
        $validated['ship_is_default'] = (bool) ($validated['ship_is_default'] ?? false);
        $validated['has_separate_billing'] = (bool) ($validated['has_separate_billing'] ?? false);

        // normalize phone to +1XXXXXXXXXX for storage
        $digits = preg_replace('/\D/', '', $validated['phone'] ?? '');
        if (strlen($digits) === 11 && Str::startsWith($digits, '1')) {
            $validated['phone'] = '+1' . substr($digits, 1);
        } elseif (strlen($digits) === 10) {
            $validated['phone'] = '+1' . $digits;
        }

        // if no separate billing, mirror shipping -> billing
        if (!$validated['has_separate_billing']) {
            $validated['bill_full_name']   = $validated['ship_full_name'] ?? null;
            $validated['bill_line1']       = $validated['ship_line1'] ?? null;
            $validated['bill_line2']       = $validated['ship_line2'] ?? null;
            $validated['bill_city']        = $validated['ship_city'] ?? null;
            $validated['bill_state']       = $validated['ship_state'] ?? null;
            $validated['bill_postal_code'] = $validated['ship_postal_code'] ?? null;
            $validated['bill_country']     = $validated['ship_country'] ?? 'United States';
        }

        // Save everything to the profile only (do NOT touch users table)
        $profile->fill($validated)->save();

        return back()->with('success', 'Profile updated!');
    }
}
