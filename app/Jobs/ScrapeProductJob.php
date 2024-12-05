<?php

namespace App\Jobs;

use App\Models\Product;
use App\Events\ProductScraped;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


class ScrapeProductJob
{
    use Dispatchable, Queueable, SerializesModels;

    protected $page;

    public function __construct($page)
    {
        $this->page = $page;
    }

    public function handle()
    {
        set_time_limit(120);

        $browser = new HttpBrowser(HttpClient::create());
        $url = 'https://www.toyshow.com.br/loja/catalogo.php?loja=460977&categoria=143&pg=' . $this->page;

        try {
            $crawler = $browser->request('GET', $url);

            $products = $crawler->filter('.product-item');
            if ($products->count() === 0) {
                Log::error("Nenhum produto encontrado na página {$this->page}");
                return;
            }

            $totalProducts = Cache::get('totalProducts', 0);

            $products->each(function (Crawler $node) use ($browser, $totalProducts) {
                try {
                    $name = $node->filter('.product-name')->count() > 0
                        ? $node->filter('.product-name')->text()
                        : 'Nome não disponível';
                    $price = $this->formatPrice($node->filter('.price')->count() > 0
                        ? $node->filter('.price')->text()
                        : 'Preço não disponível');
                    $imageSrc = $node->filter('img')->count() > 0
                        ? $node->filter('img')->attr('src')
                        : null;
                    $imageAlt = $node->filter('img')->count() > 0
                        ? $node->filter('img')->attr('alt')
                        : 'Imagem não disponível';

                    $priceOff = $node->filter('.price-off')->count() > 0
                        ? $this->formatPrice($node->filter('.price-off')->text())
                        : null;

                    if ($imageSrc && !filter_var($imageSrc, FILTER_VALIDATE_URL)) {
                        $imageSrc = 'https://www.toyshow.com.br' . $imageSrc;
                    }

                    $descriptionLink = $node->filter('.product')->count() > 0
                        ? $node->filter('.product')->attr('href')
                        : null;

                    if ($descriptionLink) {
                        $detailsCrawler = $browser->request('GET', $descriptionLink);
                        $description = $detailsCrawler->filter('.board_htm.description')->count() > 0
                            ? $detailsCrawler->filter('.board_htm.description')->text()
                            : 'Descrição não disponível';
                        $brand = $detailsCrawler->filter('.dados-valor.brand')->count() > 0
                            ? $detailsCrawler->filter('.dados-valor.brand')->text()
                            : 'Marca não disponível';
                    } else {
                        $description = 'Descrição não disponível';
                        $brand = 'Marca não disponível';
                    }

                    $existingProduct = Product::where('name', $name)
                        ->where('image_src', $imageSrc)
                        ->first();

                    if ($existingProduct) {
                        return null;
                    }

                    Product::create([
                        'name' => $name,
                        'price' => $price,
                        'price_off' => $priceOff,
                        'image_src' => $imageSrc,
                        'image_alt' => $imageAlt,
                        'description' => $description,
                        'brand' => $brand,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $scrapedProducts = Product::count();
                    Cache::put('scrapedProducts', $scrapedProducts);

                    Log::info("Emitting ProductScraped event: Scraped: {$scrapedProducts}, Total: {$totalProducts}");
                    event(new ProductScraped($scrapedProducts, $totalProducts, $this->page));

                } catch (\Exception $e) {
                    Log::error("Erro ao processar produto na página {$this->page}: {$e->getMessage()}");
                }
            });

        } catch (\Exception $e) {
            Log::error("Erro ao processar a página {$this->page}: {$e->getMessage()}");
        }
    }

    private function formatPrice($price)
    {
        $price = preg_replace('/[^0-9,\.]/', '', $price);
        return (float) str_replace(',', '.', $price);
    }
}
