<?php

use App\Livewire\Checkout;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\SocialiteController;

// Route::get('/welcome', function () {
//      return view('welcome');
//  });


Route::get('search', [SearchController::class, 'search'])->name('web.search')->middleware('throttle:30,1');



// Ana Sayfa
Route::get('/', [HomeController::class, 'index'])->name('home');

// Giriş / Kayıt
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Ürünler
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');


// Kategoriler
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('categories.show');
});



// Kampanyalar
Route::prefix('campaigns')->group(function () {
    Route::get('/', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/{slug}', [CampaignController::class, 'show'])->name('campaigns.show');
});

// markalar
Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/{brand:slug}', [BrandController::class, 'show'])->name('brands.show');
});






// OAuth Callbacks
// Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
//     ->name('socialite.redirect');
// Route::get('/auth/callback/{provider}', [SocialiteController::class, 'callback'])
//     ->name('socialite.callback');


// pages
Route::get('/about', function () {
    $about = App\Models\Page::findOrFail(1);
    $settings = App\Models\SiteSetting::first();
    return view('pages.about', compact(['about','settings']));
})->name('about');
Route::get('/faq', function () {
    $faqs = App\Models\Faq::all();
    return view('pages.faq', compact('faqs'));
})->name('faq');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');
Route::get('/privacy-policy', function () {
    return view('pages.contact');
})->name('privacy-policy');
Route::get('/terms', function () {
    return view('pages.contact');
})->name('terms');
Route::get('/shipping-policy', function () {
    return view('pages.contact');
})->name('shipping-policy');

// Favori Listesi
Route::middleware(['auth'])->group(function () {


    Route::post('products/{product}/rate', [ProductController::class, 'rate'])
    ->name('products.rate');

    Route::get('/wishlist', function () {
        $likedProducts = auth()->user()->likes()
            ->with(['product' => function ($query) {
                $query->with(['images', 'category']); // images ve category ilişkilerini de yükle
            }])
            ->get()
            ->pluck('product');

        return view('wishlist.index', compact('likedProducts'));
    })->name('wishlist.index');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Ödeme İşlemleri
    // Route::get('/checkout',   [CheckoutController::class, 'index'])->name('checkout.index');
    // Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
    Route::get('/checkout', function () {
        return view('checkout.index');
    })->name('checkout.index');


});
