<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\CampaignController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\Mobile\CartForMobileController;
use App\Http\Controllers\Api\V1\Mobile\AddressForMobileController;
use App\Http\Controllers\Api\V1\Mobile\PaymentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\SliderForHomepageForMobileController;
use App\Http\Controllers\Api\V1\Mobile\StatisticsForHomepageForMobileController;
use App\Http\Controllers\Api\V1\Mobile\IsNewProductsComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\BrandsHomepageComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\FeaturedProductsComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\CategoriesHomepageComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\MostSoldCategoriesComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\MostViewedProductsComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\BestSellingProductsComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\TestimonialsHomepageComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\MostCommentedProductsComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\MostFavoritedProductsComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\PopularProductsHomepageComponentForMobileController;
use App\Http\Controllers\Api\V1\Mobile\PopularCategoriesHomepageComponentForMobileController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// typsense search with vue component , 30 request per minute
Route::get('search', [SearchController::class, 'search'])->name('api.v1.search')->middleware('throttle:30,1');

// API version prefix
Route::prefix('v1')->group(function () {
    // Public routes
    Route::middleware('throttle:12,1')->group(function () {
        Route::post('/auth/register', [AuthController::class, 'register'])->name('api.v1.register');
        Route::post('/auth/login', [AuthController::class, 'login'])->name('api.v1.login');

        // Google OAuth routes for mobile
        Route::post('/auth/google', [AuthController::class, 'handleGoogle']);
    });


    Route::get('sliders', [SliderForHomepageForMobileController::class, 'index'])->name('api.v1.sliders.index');

    Route::get('statistics', [StatisticsForHomepageForMobileController::class, 'index'])->name('api.v1.mobile.statistics');

    Route::get('most-viewed-products', [MostViewedProductsComponentForMobileController::class, 'index'])->name('api.v1.most-viewed-products');

    Route::get('best-selling-products', [BestSellingProductsComponentForMobileController::class, 'index'])->name('api.v1.best-selling-products');

    Route::get('most-favorited-products', [MostFavoritedProductsComponentForMobileController::class, 'index'])->name('api.v1.most-favorited-products');

    Route::get('most-commented-products', [MostCommentedProductsComponentForMobileController::class, 'index'])->name('api.v1.most-commented-products');

    Route::get('most-sold-categories', [MostSoldCategoriesComponentForMobileController::class, 'index'])->name('api.v1.most-sold-categories');

    Route::get('featured-products', [FeaturedProductsComponentForMobileController::class, 'index'])->name('api.v1.featured-products');

    Route::get('homepage-categories', [CategoriesHomepageComponentForMobileController::class, 'index'])->name('api.v1.homepage-categories');

    Route::get('new-products', [IsNewProductsComponentForMobileController::class, 'index'])->name('api.v1.new-products');

    Route::get('homepage-brands', [BrandsHomepageComponentForMobileController::class, 'index'])->name('api.v1.homepage-brands');

    Route::get('homepage-testimonials', [TestimonialsHomepageComponentForMobileController::class, 'index'])->name('api.v1.homepage-testimonials');

    Route::get('popular-categories', [PopularCategoriesHomepageComponentForMobileController::class, 'index'])->name('api.v1.popular-categories');

    Route::get('popular-products', [PopularProductsHomepageComponentForMobileController::class, 'index'])->name('api.v1.popular-products');
    // Cart routes with permission middleware
    Route::prefix('cart')
        ->middleware(['auth:sanctum', 'verified'])
        ->group(function () {
            Route::group(['middleware' => ['can:cart:view']], function () {
                Route::get('/', [CartForMobileController::class, 'index'])->name('api.v1.mobile.cart.index');
            });
            Route::group(['middleware' => ['can:cart:add']], function () {
                Route::post('/', [CartForMobileController::class, 'store'])->name('api.v1.mobile.cart.store');
            });
            Route::group(['middleware' => ['can:cart:update']], function () {
                Route::put('/{productId}', [CartForMobileController::class, 'update'])->name('api.v1.mobile.cart.update');
            });
            Route::group(['middleware' => ['can:cart:remove']], function () {
                Route::delete('/{productId}', [CartForMobileController::class, 'destroy'])->name('api.v1.mobile.cart.destroy');
            });
            Route::group(['middleware' => ['can:product:purchase']], function () {
                Route::post('/apply-coupon', [CartForMobileController::class, 'applyCoupon'])->name('api.v1.mobile.cart.apply-coupon');
            });
        });

    Route::post('payment', [PaymentForMobileController::class, 'processPayment'])->name('api.v1.payment.process');

    Route::apiResource('addresses', AddressForMobileController::class)
        ->names([
            'index' => 'api.v1.mobile.addresses.index',
            'store' => 'api.v1.mobile.addresses.store',
            'show' => 'api.v1.mobile.addresses.show',
            'update' => 'api.v1.mobile.addresses.update',
            'destroy' => 'api.v1.mobile.addresses.destroy',
        ])
        ->middleware(['can:address:view', 'can:address:create', 'can:address:update', 'can:address:delete']);

    Route::get('products', [ProductController::class, 'index'])->name('api.v1.products.index');
    Route::get('products/{slug}', [ProductController::class, 'show'])->name('api.v1.products.show');
    Route::get('categories', [CategoryController::class, 'index'])->name('api.v1.categories.index');
    Route::get('categories/{slug}', [CategoryController::class, 'show'])->name('api.v1.categories.show');
    Route::get('brands', [BrandController::class, 'index'])->name('api.v1.brands.index');
    Route::get('brands/{slug}', [BrandController::class, 'show'])->name('api.v1.brands.show');
    Route::get('campaigns', [CampaignController::class, 'index'])->name('api.v1.campaigns.index');
    Route::get('campaigns/{slug}', [CampaignController::class, 'show'])->name('api.v1.campaigns.show');

    // Authenticated routes
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::delete('auth/logout', [AuthController::class, 'logout'])->name('api.v1.logout');
        Route::get('auth/refresh', [AuthController::class, 'refresh'])->name('api.v1.refresh.auth.token');

        Route::get('profile', [AuthController::class, 'user'])->name('api.v1.profile');
    });
    // Route::apiResource('addresses', AddressController::class)->names(['index' => 'api.v1.addresses.index', 'store' => 'api.v1.addresses.store', 'show' => 'api.v1.addresses.show', 'update' => 'api.v1.addresses.update', 'destroy' => 'api.v1.addresses.destroy',]);
    Route::prefix('products/{productId}')->group(function () {
        Route::apiResource('comments', CommentController::class)->names(['index' => 'api.v1.comments.index', 'store' => 'api.v1.comments.store', 'update' => 'api.v1.comments.update', 'destroy' => 'api.v1.comments.destroy',]);
        Route::get('comments/user-info', [CommentController::class, 'userCommentsInfo'])->name('api.v1.comments.user-info');
    });
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('api.v1.checkout.store');
    Route::get('wishlist', [LikeController::class, 'index']);
    Route::post('wishlist/{productId}', [LikeController::class, 'store']);
    Route::delete('wishlist/{productId}', [LikeController::class, 'destroy']);
    Route::get('wishlist/check/{productId}', [LikeController::class, 'check']);
    // Route::get('cart', [CartController::class, 'index']);
    // Route::post('cart', [CartController::class, 'store']);
    // Route::put('cart/{productId}', [CartController::class, 'update']);
    // Route::delete('cart/{productId}', [CartController::class, 'destroy'])->name('api.v1.cart.destroy');
    // Route::get('products/{productId}/comments', [CommentController::class, 'index'])->name('api.v1.comments.index');
    // Route::post('products/{productId}/comments', [CommentController::class, 'store'])->name('api.v1.comments.store');
    // Route::put('products/{productId}/comments/{commentId}', [CommentController::class, 'update'])->name('api.v1.comments.update');
    // Route::delete('products/{productId}/comments/{commentId}', [CommentController::class, 'destroy'])->name('api.v1.comments.destroy');
    // Route::get('products/{productId}/comments-info', [CommentController::class, 'userCommentsInfo'])->name('api.v1.comments.info');
    // Route::post('comments/{productId}', [CommentController::class, 'store'])->name('api.v1.comments.store');
    // Route::delete('comments/{productId}/{commentId}', [CommentController::class, 'destroy'])->name('api.v1.comments.destroy');

})->middleware('throttle:60,1');
