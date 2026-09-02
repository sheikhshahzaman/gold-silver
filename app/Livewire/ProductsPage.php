<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Cart;
use App\Services\Rates\RatesProvider;
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

    /**
     * Product ID -> "Added!" flash, cleared on the next render. Lets the
     * Add-to-Cart button momentarily show feedback after a click.
     */
    public array $justAdded = [];

    public function mount(): void
    {
        $this->refreshPrices();

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

    public function refreshPrices(): void
    {
        $allPrices = app(RatesProvider::class)->getAllPrices();
        $this->goldPrices = $allPrices['gold'] ?: [];
        $this->silverPrices = $allPrices['silver'] ?: [];
    }

    /** Add a product to the cart and flash a confirmation badge on its card. */
    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;
        $cart = app(Cart::class);
        if ($cart->unitPriceFor($product) === null) {
            $this->addError('cart', 'This product price is not available right now.');
            return;
        }

        $cart->add($product, 1);
        $this->justAdded[$productId] = true;
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $this->refreshPrices();

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
