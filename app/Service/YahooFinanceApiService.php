<?php

namespace App\Service;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;


class YahooFinanceApiService
{
    private $client = null;

    private function __construct() {
        $this->client = ApiClientFactory::createApiClient();
    }

    public function splitData(string $symbol)
    {
        return $this->client->getHistoricalSplitData(
            $symbol,
            new \DateTime("-5 years"),
            new \DateTime("today")
        );
    }
}

