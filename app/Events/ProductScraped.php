<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ProductScraped
{
    use SerializesModels;

    public $scrapedProducts;
    public $totalProducts;
    public $page;


    public function __construct($scrapedProducts, $totalProducts, $page)
    {
        $this->scrapedProducts = $scrapedProducts;
        $this->totalProducts = $totalProducts;
        $this->page = $page;
    }

}
