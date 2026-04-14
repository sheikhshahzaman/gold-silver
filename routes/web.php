<?php

use App\Livewire\BuyPage;
use App\Livewire\CheckoutPage;
use App\Livewire\ContactPage;
use App\Livewire\HomePage;
use App\Livewire\OrderConfirmationPage;
use App\Livewire\ProductsPage;
use App\Livewire\SellPage;
use App\Livewire\SpotPricePage;
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
Route::get('/more', fn () => view('pages.more'))->name('more');

// Static/dynamic pages loaded from the Page model
$staticPageSlugs = ['about-us', 'disclaimer', 'privacy-policy', 'terms-and-conditions'];

foreach ($staticPageSlugs as $slug) {
    Route::get("/{$slug}", function () use ($slug) {
        $page = \App\Models\Page::where('slug', $slug)->firstOrFail();
        return view('pages.show', compact('page'));
    })->name("page.{$slug}");
}
