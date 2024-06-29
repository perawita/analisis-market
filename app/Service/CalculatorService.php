<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class CalculatorService extends YahooFinanceApiService
{
    /**
     * Handles the calculator result request.
     *
     * @param Request|null $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $stock_symbol = $request->input('stock_symbol');
        $current_stock_price = $this->getQuote($stock_symbol)['regularMarketPrice'];

        if ($stock_symbol && $current_stock_price) {
            $request_results = $this->calculateValuation($stock_symbol, $current_stock_price);
            return view('Pages.Calculator', [
                'results' => $request_results,
            ]);
        } else {
            return view('Pages.Calculator', [
                'results' => null
            ]);
        }
    }

    /**
     * Calculates the intrinsic value, current stock price, and margin of safety.
     * 
     * @param float $eps
     * @param float $growth_rate
     * @param float $current_stock_price
     * @return array An array containing the intrinsic value, current stock price, and margin of safety.
     */
    private function calculate($eps, $growth_rate, $current_stock_price): array
    {
        $intrinsic_value = $eps * (8.5 + 2 * $growth_rate);
        $margin_of_safety = (($intrinsic_value - $current_stock_price) / $intrinsic_value) * 100;

        return [  
            'intrinsik' => $intrinsic_value,
            'harga' => $current_stock_price,
            'mos' => (string)$margin_of_safety . '%',
        ];
    }

    /**
     * Fetches the average growth rate for the last 5 periods (QoQ or YoY).
     * 
     * @param string $stock_symbol
     * @param string $type ('QoQ' or 'YoY')
     * @return float The average growth rate.
     */
    private function getAverageGrowthRate(string $stock_symbol, string $type = 'YoY'): float
    {
        // Dummy data representing fetched data for EPS over last 5 periods
        $eps_data = [
            'AAPL' => [1.5, 1.6, 1.7, 1.8, 1.9], // Example data for Apple
            'MSFT' => [2.0, 2.1, 2.2, 2.3, 2.4], // Example data for Microsoft
            // Add more stocks as needed
        ];

        if (!isset($eps_data[$stock_symbol])) {
            throw new Exception('Stock symbol not found.');
        }

        $eps_values = $eps_data[$stock_symbol];
        $growth_rates = [];

        for ($i = 1; $i < count($eps_values); $i++) {
            if ($type === 'QoQ') {
                $growth_rates[] = (($eps_values[$i] - $eps_values[$i - 1]) / $eps_values[$i - 1]) * 100;
            } else {
                $growth_rates[] = (($eps_values[$i] - $eps_values[0]) / $eps_values[0]) * 100;
            }
        }

        return array_sum($growth_rates) / count($growth_rates);
    }

    /**
     * Calculates the valuation for a given stock symbol.
     * 
     * @param string $stock_symbol
     * @param float $current_stock_price
     * @return array The calculated valuation data.
     */
    public function calculateValuation(string $stock_symbol, float $current_stock_price): array
    {
        // Dummy data representing EPS for different stocks
        $eps_data = $this->getQuote($stock_symbol)['epsTrailingTwelveMonths'];

        if (!isset($eps_data)) {
            throw new Exception('Stock symbol not found.');
        }

        $eps = $eps_data;
        $growth_rate = $this->getAverageGrowthRate($stock_symbol);

        return $this->calculate($eps, $growth_rate, $current_stock_price);
    }
}