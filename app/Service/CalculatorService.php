<?php

namespace App\Service;

use Exception;

class CalculatorService extends YahooFinanceApiService
{
    /**
     * Handles the calculator result request.
     *
     * @param string $symbol
     * @return array|null
     */
    public function index($symbol)
    {
        $stock_symbol = $symbol;
        $regular_market_price = $this->getQuote($stock_symbol);
        $current_stock_price = $regular_market_price->getRegularMarketPrice() ?? 0.00;


        if ($stock_symbol && $current_stock_price) {
            $request_results = $this->calculateValuation($stock_symbol, $current_stock_price);
            return $request_results;
        } else {
            return null;
        }
    }

    /**
     * Calculates the intrinsic value, current stock price, and margin of safety.
     * 
     * @param float $eps
     * @param float $growth_rate
     * @param float $current_stock_price
     * @return array
     */
    private function calculate(float $eps, float $growth_rate, float $current_stock_price): array
    {
        $intrinsic_value = ($eps * (8.5 + 2 * $growth_rate) / 73.22);
        $margin_of_safety = (($intrinsic_value - $current_stock_price) / $intrinsic_value) * 100;

        return [
            'intrinsik' => $intrinsic_value,
            'harga' => $current_stock_price,
            'mos' => number_format($margin_of_safety, 2) . '%', // Formatting margin of safety
            'eps' => $eps,
            'growth_rate' => $growth_rate,
            'stock_symbol' => ''
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
        // Simulated function to fetch EPS data, replace with actual implementation
        $eps_values = $this->eps($stock_symbol);
        $valid_eps_values = [];
        foreach ($eps_values as $eps_value) {
            if ($eps_value !== "--") {
                $valid_eps_values[] = $eps_value;
            }
        }

        $growth_rates = [];
        for ($i = 1; $i < count($valid_eps_values); $i++) {
            if ($type === 'QoQ') {
                $growth_rates[] = (($valid_eps_values[$i] - $valid_eps_values[$i - 1]) / $valid_eps_values[$i - 1]) * 100;
            } else {
                $growth_rates[] = (($valid_eps_values[$i] - $valid_eps_values[0]) / $valid_eps_values[0]) * 100;
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
        // Dummy EPS data, replace with actual implementation
        $eps_market = $this->getQuote($stock_symbol);
        $eps_data = $eps_market->getEpsTrailingTwelveMonths();
        $eps = $eps_data; // Dummy EPS value, replace with actual fetched EPS
        $growth_rate = $this->getAverageGrowthRate($stock_symbol);

        return $this->calculate($eps, $growth_rate, $current_stock_price);
    }
}
