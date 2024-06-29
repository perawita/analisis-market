<?php

namespace App\Service;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;

class YahooFinanceApiService
{
    private $client;

    public function __construct() {
        $this->client = ApiClientFactory::createApiClient();
    }

    public function splitData($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        $splitData = $this->client->search($symbol);
        return $splitData;
    }
}
