<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerService
{
    public function main($url)
    {

        $client = HttpClient::create();

        $response = $client->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'max_redirects' => 10,
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            return new Crawler($response->getContent());
        } else {
            throw new \Exception("Error: Received status code $statusCode");
        }
    }
}
