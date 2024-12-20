<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Product Detail - Belanja Cuan')]
class ProductDetailPage extends Component
{
    public string $slug;

    // mount 
    public function mount(string $slug)
    {
        $this->slug = $slug;
    }
    public function render()
    {
        return view('livewire.product-detail-page',[
            'product' => Product::where('slug',$this->slug)->firstOrFail()
        ]);
    }
}
