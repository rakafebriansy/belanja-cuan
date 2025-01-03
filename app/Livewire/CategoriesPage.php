<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Categories - Belanja Cuan')]
class CategoriesPage extends Component
{
    public function render()
    {
        return view('livewire.categories-page',[
            'categories' => Category::where('is_active',1)->get()
        ]);
    }
}
