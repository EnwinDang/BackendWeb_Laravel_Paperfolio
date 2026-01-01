<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

// Public routes
Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [\App\Http\Controllers\NewsController::class, 'show'])->name('news.show');
Route::get('/faq', [\App\Http\Controllers\FaqController::class, 'index'])->name('faq.index');
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');
Route::get('/profile/{user}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/portfolio', [\App\Http\Controllers\PortfolioController::class, 'index'])->name('portfolio.index');
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets/{asset}/buy', [TradeController::class, 'buy'])->name('trades.buy');
    Route::post('/assets/{asset}/sell', [TradeController::class, 'sell'])->name('trades.sell');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('assets', AssetController::class)->except([
        'index', 'show'
    ]);
    
    // User management
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/toggle-admin', [\App\Http\Controllers\UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    
    // News management
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
});
