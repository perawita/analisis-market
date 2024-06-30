<?php

namespace App\Service;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use DateTime;

use App\Service\CrawlerService;

class YahooFinanceApiService 
{
    private $client = null;

    public function __construct() {
        $this->client = ApiClientFactory::createApiClient();
    }

    public function getQuote($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

        $get_split = $this->client->getQuote($symbol);
        return $get_split;
    }

    public function eps($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/financials/';

        $crawler_service = new CrawlerService;
        $crawler = $crawler_service->main($url);
    
        $response = $crawler->filter('div.tableContainer.svelte-1pgoo1f div.table.svelte-1pgoo1f')->each(function ($table) {
            $labels = $table->filter('div.tableHeader.svelte-1pgoo1f')->first()->filter('div.column.svelte-1ezv2n5')->each(function ($column) {
                return $column->text();
            });


            $values = $table->filter('div.tableBody.svelte-1pgoo1f div.row.lv-0.svelte-1xjz32c')->each(function ($row) {
                return $row->filter('div.column.svelte-1xjz32c')->each(function ($column) {
                    return $column->text();
                });
            });

            return [
                'labels' => $labels,
                'values' => $values,
            ];
        });
        

        $index = array_search("Basic EPS", array_column($response[0]['values'], 0));

        // Jika ditemukan, ambil nilai "Basic EPS"
        if ($index !== false) {
            $basic_eps_values = array_slice($response[0]['values'][$index], 1); // Mengambil nilai "Basic EPS" mulai dari indeks ke-1
            return $basic_eps_values;
        }
    }
    
    

    public function historicalData($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

         $get_histori = $this->client->getHistoricalData(
            $symbol, 
            ApiClient::INTERVAL_1_DAY, 
            new \DateTime("-5 years"),
            new DateTime('today')
        );

        return $get_histori;
    }

    public function optionChain($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        return $this->client->getOptionChain($symbol);
    }
}
