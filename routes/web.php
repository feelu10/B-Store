<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Auth\VerifyEmailLinkController;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| Public Shop Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.show');

// Public (or semi-public) product stock endpoint (AJAX-friendly)
Route::get('/products/{product}/stock', [ShopController::class, 'stock'])->name('products.stock');

/*
|--------------------------------------------------------------------------
| Auth Scaffolding
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/
// Notice page instructing the user to verify email
Route::get('/email/verify', fn() => view('auth.verify-email'))
    ->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    if (! $request->hasValidSignature()) {
        return redirect()->route('login')
            ->withErrors(['email' => 'Verification link expired or invalid. Please request a new one.']);
    }

    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return redirect()->route('login')
            ->withErrors(['email' => 'Invalid verification link.']);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('login')->with('status', 'Email already verified. You can sign in.');
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    return redirect()->route('login')->with('status', 'Email verified! You can now sign in.');
})
->middleware(['signed', 'throttle:6,1'])
->name('verification.verify');

// Resend verification link
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Post-Login Landing (Verified Only)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    // Role-aware landing
    if (auth()->check() && auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Customer Routes (Verified + Role: customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:customer'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');

    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders.my');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Verified + Role: admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');

    Route::resource('categories', AdminCategoryController::class);
    Route::resource('products', AdminProductController::class);
    Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])
        ->name('products.images.destroy');

    // Admin can view & manage customer orders
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
});
