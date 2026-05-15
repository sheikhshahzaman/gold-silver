<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\PriceEngine\PriceCacheManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Shop Gold & Silver Products - Islamabad Bullion Exchange')]
class ProductsPage extends Component
{
    public string $categoryId = 'all';
    public array $goldPrices = [];
    public array $silverPrices = [];

    public function mount(): void
    {
        $cacheManager = app(PriceCacheManager::class);
        $allPrices = $cacheManager->getAllPrices();
        $this->goldPrices = $allPrices['gold'] ?: [];
        $this->silverPrices = $allPrices['silver'] ?: [];

        // Honour ?category=<slug> from incoming links (e.g. the home-page chips).
        $slug = request()->query('category');
        if ($slug && $slug !== 'all') {
            $cat = ProductCategory::where('slug', $slug)->first();
            if ($cat) {
                $this->categoryId = (string) $cat->id;
            }
        }
    }

    public function setCategory(string $id): void
    {
        $this->categoryId = $id;
    }

    public function render()
    {
        $query = Product::with('productCategory')->active()->ordered();

        if ($this->categoryId !== 'all') {
            $query->where('category_id', $this->categoryId);
        }

        return view('livewire.products-page', [
            'products' => $query->get(),
            'categories' => ProductCategory::active()->ordered()->get(),
        ]);
    }
}
