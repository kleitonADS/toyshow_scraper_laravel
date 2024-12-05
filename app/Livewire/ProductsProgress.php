<?php

namespace App\Livewire;

use Livewire\Component;
use App\Events\ProductScraped;
use Illuminate\Support\Facades\Cache;

class ProductsProgress extends Component
{
    public $scrapedProducts = 0;
    public $totalProducts = 0;

    protected $listeners = ['updateProgress' => 'updateProgress', ProductScraped::class => 'handleProductScraped'];



    public function mount()
    {
        $this->scrapedProducts = Cache::get('scrapedProducts', 0);
        $this->totalProducts = Cache::get('totalProducts', 0);
    }


    public function handleProductScraped($scrapedProducts, $totalProducts, $page)
    {

        $this->scrapedProducts = $scrapedProducts;
        $this->totalProducts = $totalProducts;

        Cache::put('scrapedProducts', $scrapedProducts);
        Cache::put('totalProducts', $totalProducts);
    }

    public function render()
    {
        return view('livewire.products-progress');
    }
}
