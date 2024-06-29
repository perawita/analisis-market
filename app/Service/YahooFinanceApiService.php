<?php

namespace App\Service;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;


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

    public function historicalData($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

         $get_histori = $this->client->getHistoricalData(
            $symbol, 
            ApiClient::INTERVAL_1_DAY, 
            new DateTime('-14 days'), 
            new DateTime('today')
        );

        return $get_histori;
    }
}
