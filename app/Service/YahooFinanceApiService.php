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
        $get_split = $this->client->search('Apple');
        return $get_split;
    }
}

