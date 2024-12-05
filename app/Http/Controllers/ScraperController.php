<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Jobs\ScrapeProductJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Cache;

class ScraperController extends Controller
{
    public function index()
    {
        $scrapedProducts = Product::count();
        Cache::put('scrapedProducts', $scrapedProducts);
        return view('index');
    }

    public function start(Request $request)
    {
        $browser = new HttpBrowser(HttpClient::create());
        $urlBase = 'https://www.toyshow.com.br/loja/catalogo.php?loja=460977&categoria=143&pg=1';

        try {
            $crawler = $browser->request('GET', $urlBase);

            $totalProducts = $crawler->filter('.products-in-page span')->eq(1)->count() > 0
                ? (int) filter_var($crawler->filter('.products-in-page span')->eq(0)->text(), FILTER_SANITIZE_NUMBER_INT)
                : 0;

            cache()->put('totalProducts', $totalProducts);

            $totalPagesText = $crawler->filter('.products-in-page > span')->eq(1)->text();
            $totalPages = preg_match('/\d+/', $totalPagesText, $matches) ? (int) $matches[0] : 1;

            $this->dispatchProgressUpdate(0, $totalPages, $totalProducts);

            Log::info("Iniciando scraping de $totalProducts produtos em $totalPages páginas.");

            for ($page = 1; $page <= $totalPages; $page++) {
                ScrapeProductJob::dispatch($page);
            }

            return response()->json([
                'message' => 'Scraping Finalizado.'
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao iniciar o scraping: {$e->getMessage()}");

            return response()->json([
                'message' => 'Erro ao processar o scraping.'
            ], 500);
        }
    }

    public function stopScraper()
    {
        session(['scraper_status' => 'stopped']);

        if (session('scraper_running')) {
            session(['scraper_running' => false]);
        }

        return redirect()->route('index')->with('message', 'Scraping interrompido.');
    }

    private function dispatchProgressUpdate($scrapedProducts, $totalPages, $totalProducts)
    {
        session([
            'scrapedProducts' => $scrapedProducts,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
        ]);
    }
}
