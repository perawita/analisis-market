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

    public function splitData($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

        $get_split = $this->client->getQuote('AAPL');
        return $get_split;
    }
}

