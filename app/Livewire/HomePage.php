<?php

namespace App\Livewire;

use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Services\PriceEngine\PriceCacheManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Islamabad Bullion Exchange - Pakistan\'s Premier Gold & Silver Exchange')]
class HomePage extends Component
{
    public array $goldPrices = [];
    public array $silverPrices = [];
    public array $internationalRates = [];
    public array $currencyRates = [];

    public function mount(): void
    {
        $cacheManager = app(PriceCacheManager::class);
        $allPrices = $cacheManager->getAllPrices();

        $this->goldPrices = $allPrices['gold'] ?: [];
        $this->silverPrices = $allPrices['silver'] ?: [];
        $this->internationalRates = $allPrices['international'] ?: [];
        $this->currencyRates = $allPrices['currencies'] ?: [];
    }

    public function render()
    {
        return view('livewire.home-page', [
            'heroSlides' => HeroSlide::active()->ordered()->limit(4)->get(),
            'products' => Product::with('productCategory')->active()->ordered()->limit(4)->get(),
            'services' => Service::active()->ordered()->get(),
            'testimonials' => Testimonial::active()->ordered()->get(),
            'trackRecord' => [
                'years' => Setting::get('track_years', '10+'),
                'customers' => Setting::get('track_customers', '50,000+'),
                'authentic' => Setting::get('track_authentic', '100%'),
                'rating' => Setting::get('track_rating', '4.8★'),
            ],
            'contactInfo' => [
                'address' => Setting::get('contact_address', 'Shop No 1, Ground Floor, Trade Center<br>F-7 Markaz Block 20-B, Islamabad 44210'),
                'phone' => Setting::get('contact_phone', '0340 2786222'),
                'hours' => Setting::get('contact_hours', 'Mon–Sat: 12:00 PM – 9:00 PM<br>Sunday: Closed'),
                'website' => Setting::get('contact_website', 'islamabadbullionexchange.com'),
            ],
        ]);
    }
}
