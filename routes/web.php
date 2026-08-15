<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Public routes
Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/faq', [\App\Http\Controllers\FaqController::class, 'index'])->name('faq.index');
Route::get('/leaderboard', [\App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');

// Contact form - accessible to everyone (including non-logged-in users), except admins
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

// Comment routes (authenticated users only)
Route::middleware(['auth'])->group(function () {
    Route::post('/news/{news}/comments', [\App\Http\Controllers\NewsController::class, 'storeComment'])->name('news.comments.store');
    Route::delete('/news/comments/{comment}', [\App\Http\Controllers\NewsController::class, 'destroyComment'])->name('news.comments.destroy');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Password reset routes
Route::get('/password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/portfolio', [\App\Http\Controllers\PortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('/portfolio/restart', [\App\Http\Controllers\PortfolioController::class, 'restart'])->name('portfolio.restart');

    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets/{asset}/buy', [TradeController::class, 'buy'])->name('trades.buy');
    Route::post('/assets/{asset}/sell', [TradeController::class, 'sell'])->name('trades.sell');

    // Price alerts - proxied to the standalone Price Alerts Node.js API
    Route::get('/price-alerts', [\App\Http\Controllers\PriceAlertController::class, 'index'])->name('price-alerts.index');
    Route::post('/price-alerts', [\App\Http\Controllers\PriceAlertController::class, 'store'])->name('price-alerts.store');
    Route::delete('/price-alerts/{id}', [\App\Http\Controllers\PriceAlertController::class, 'destroy'])->name('price-alerts.destroy');

    // Leveraged positions
    Route::post('/assets/{asset}/positions/open', [PositionController::class, 'open'])->name('positions.open');
    Route::post('/positions/{position}/close', [PositionController::class, 'close'])->name('positions.close');

    // Watchlist routes (only for regular users)
    Route::post('/assets/{asset}/watchlist/add', [AssetController::class, 'addToWatchlist'])->name('assets.watchlist.add');
    Route::post('/assets/{asset}/watchlist/remove', [AssetController::class, 'removeFromWatchlist'])->name('assets.watchlist.remove');

    // Social feed
    Route::get('/feed', [PostController::class, 'index'])->name('feed.index');
    Route::post('/feed', [PostController::class, 'store'])->name('feed.store');
    Route::post('/feed/{post}/like', [PostController::class, 'like'])->name('feed.like');
    Route::post('/feed/{post}/unlike', [PostController::class, 'unlike'])->name('feed.unlike');
    Route::delete('/feed/{post}', [PostController::class, 'destroy'])->name('feed.destroy');

    // Messages - must come before /profile/{user} to avoid route conflicts
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [\App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
});

Route::middleware(['auth'])->group(function () {
    // Profile edit routes - must come before /profile/{user} to avoid route conflicts
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// Public route for showing profiles - must come AFTER /profile/edit to avoid route conflicts
Route::get('/profile/{user}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('assets', AssetController::class)->except([
        'index', 'show'
    ]);
    
    // User management
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/toggle-admin', [\App\Http\Controllers\UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    
    // News management - specific routes must come before parameterized routes
    Route::get('/news/create', [\App\Http\Controllers\NewsController::class, 'create'])->name('news.create');
    Route::post('/news', [\App\Http\Controllers\NewsController::class, 'store'])->name('news.store');
    Route::get('/news/{news}/edit', [\App\Http\Controllers\NewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{news}', [\App\Http\Controllers\NewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{news}', [\App\Http\Controllers\NewsController::class, 'destroy'])->name('news.destroy');
    
    // FAQ management
    Route::get('/faq/category/create', [\App\Http\Controllers\FaqController::class, 'createCategory'])->name('faq.category.create');
    Route::post('/faq/category', [\App\Http\Controllers\FaqController::class, 'storeCategory'])->name('faq.category.store');
    Route::get('/faq/category/{faqCategory}/edit', [\App\Http\Controllers\FaqController::class, 'editCategory'])->name('faq.category.edit');
    Route::put('/faq/category/{faqCategory}', [\App\Http\Controllers\FaqController::class, 'updateCategory'])->name('faq.category.update');
    Route::delete('/faq/category/{faqCategory}', [\App\Http\Controllers\FaqController::class, 'destroyCategory'])->name('faq.category.destroy');
    
    Route::get('/faq/item/create', [\App\Http\Controllers\FaqController::class, 'createItem'])->name('faq.item.create');
    Route::post('/faq/item', [\App\Http\Controllers\FaqController::class, 'storeItem'])->name('faq.item.store');
    Route::get('/faq/item/{faqItem}/edit', [\App\Http\Controllers\FaqController::class, 'editItem'])->name('faq.item.edit');
    Route::put('/faq/item/{faqItem}', [\App\Http\Controllers\FaqController::class, 'updateItem'])->name('faq.item.update');
    Route::delete('/faq/item/{faqItem}', [\App\Http\Controllers\FaqController::class, 'destroyItem'])->name('faq.item.destroy');
    
    // Contact submissions management
    Route::get('/contact/submissions', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
    Route::get('/contact/submissions/{contactSubmission}', [\App\Http\Controllers\ContactController::class, 'showSubmission'])->name('contact.show-submission');
    Route::post('/contact/submissions/{contactSubmission}/respond', [\App\Http\Controllers\ContactController::class, 'respond'])->name('contact.respond');
    Route::post('/contact/submissions/{contactSubmission}/mark-read', [\App\Http\Controllers\ContactController::class, 'markAsRead'])->name('contact.mark-read');
    Route::post('/contact/submissions/{contactSubmission}/mark-unread', [\App\Http\Controllers\ContactController::class, 'markAsUnread'])->name('contact.mark-unread');
    Route::delete('/contact/submissions/{contactSubmission}', [\App\Http\Controllers\ContactController::class, 'destroy'])->name('contact.destroy');
});

// Public route for showing news - must come AFTER /news/create to avoid route conflicts
Route::get('/news/{news}', [\App\Http\Controllers\NewsController::class, 'show'])->name('news.show');

// Asset trading terminal - must come AFTER /assets/create and /assets/{asset}/edit to avoid route conflicts
Route::middleware(['auth'])->group(function () {
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
});
