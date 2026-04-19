<?php

use App\Livewire\BuyPage;
use App\Livewire\CheckoutPage;
use App\Livewire\ContactPage;
use App\Livewire\HomePage;
use App\Livewire\OrderConfirmationPage;
use App\Livewire\ProductsPage;
use App\Livewire\ScanQrPage;
use App\Livewire\SellPage;
use App\Livewire\SpotPricePage;
use App\Livewire\VerifyItemPage;
use App\Livewire\VerifySerialPage;
use App\Livewire\ZakatCalculatorPage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Main pages
Route::get('/', HomePage::class)->name('home');
Route::get('/live', SpotPricePage::class)->name('live');
Route::get('/products', ProductsPage::class)->name('products');
Route::get('/buy', BuyPage::class)->name('buy');
Route::get('/sell', SellPage::class)->name('sell');
Route::get('/contact', ContactPage::class)->name('contact');
Route::get('/zakat-calculator', ZakatCalculatorPage::class)->name('zakat');
Route::get('/checkout/{orderNumber}', CheckoutPage::class)->name('checkout');
Route::get('/order/{orderNumber}', OrderConfirmationPage::class)->name('order.show');
Route::get('/scan', ScanQrPage::class)->name('scan');
Route::get('/verify', VerifySerialPage::class)->name('verify.serial');
Route::get('/verify/{token}', VerifyItemPage::class)->name('verify');
Route::get('/more', fn () => view('pages.more'))->name('more');

// Static pages with dedicated templates
Route::get('/about-us', fn () => view('pages.about-us'))->name('page.about-us');
Route::get('/disclaimer', fn () => view('pages.disclaimer'))->name('page.disclaimer');
Route::get('/privacy-policy', fn () => view('pages.privacy-policy'))->name('page.privacy-policy');
Route::get('/terms-and-conditions', fn () => view('pages.terms-and-conditions'))->name('page.terms-and-conditions');
