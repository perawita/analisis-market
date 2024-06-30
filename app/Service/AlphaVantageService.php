<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class AlphaVantageService
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('ALPHA_VANTAGE_API_KEY');
    }

    public function getEPS($symbol)
    {
        $url = "https://www.alphavantage.co/query?function=INCOME_STATEMENT&symbol=$symbol&apikey={$this->apiKey}";
        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getSharesOutstanding($symbol)
    {
        $url = "https://www.alphavantage.co/query?function=OVERVIEW&symbol=$symbol&apikey={$this->apiKey}";
        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }


        return null;
    }
}
