<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Product Detail - Belanja Cuan')]
class ProductDetailPage extends Component
{
    use LivewireAlert;
    public string $slug;

    public $quantity = 1;

    public function mount(string $slug)
    {
        $this->slug = $slug;
    }
    public function increaseQty()
    {
        $this->quantity++;
    }
    public function decreaseQty()
    {
        if($this->quantity > 1) {
            $this->quantity--;
        }
    }
    public function addToCart($product_id) 
    {
        $total_count = CartManagement::addItemToCartWithQty($product_id, $this->qty);
        $this->dispatch('update-cart-count',total_count: $total_count)->to(Navbar::class);
        $this->alert('success','Product added to cart.',[
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
    public function render()
    {
        return view('livewire.product-detail-page',[
            'product' => Product::where('slug',$this->slug)->firstOrFail()
        ]);
    }
}
