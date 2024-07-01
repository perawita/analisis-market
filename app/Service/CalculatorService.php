<?php

namespace App\Service;

use Exception;

class CalculatorService extends YahooFinanceApiService
{
    public function index($symbol)
    {
        $intrinsic_value = $this->intrinsik($symbol);
        $mos_value = $this->calculate_mos($symbol);

        return [
            'intrinsic_value' => $intrinsic_value,
            'mos_value' => $mos_value,
            'stock_price' => $this->live_price($symbol)
        ];
    }

    private function intrinsik($symbol)
    {
        $bond_yield = 4;
        $growth_rate = $this->get_growth_rate($symbol);
        $eps = $this->eps($symbol);
        return [
            'eps' => $eps,
            'growth_rate' => $growth_rate,
            'bond_yield' => $bond_yield,
            'intrinsik' => ($eps * (8.5 + 2 * $growth_rate) * 4.4) / $bond_yield
        ];
    }

    private function calculate_mos($symbol) {
        $stock_price  = $this->live_price($symbol);
        $intrinsic_value = ($this->intrinsik($symbol))['intrinsik'];

        return [
            'stock_price' => $stock_price,
            'intrinsic_value' => $intrinsic_value,
            'mos' => (($intrinsic_value - $stock_price) / $intrinsic_value) * 100
        ];
    }

    private function get_growth_rate($symbol)
    {
        $present = $this->cash_flow_now($symbol);
        $past = $this->cash_flow_one_year_ago($symbol);

        // Hilangkan koma dan titik dari nilai kas
        $present = (float)str_replace(',', '', $present);
        $past = (float)str_replace(',', '', $past);

        return $growth_rate = (($present - $past) / $past) * 100;
    }

    private function rasio_price_earning($symbol)
    {
        $stock_price  = $this->live_price($symbol);
        $eps = $this->eps($symbol);

        return $stock_price / $eps;
    }
}
