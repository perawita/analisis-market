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
        // // Mendapatkan data harga saham dari metode lain dalam kelas ini
        // $data = $this->cash_flow_history($symbol);

        // // Ekstrak data harga Adj Close dan tanggal
        // $adjClosePrices = array_column($data, 5); // Kolom 5 adalah 'Adj Close'
        // $dates = array_column($data, 0); // Kolom 0 adalah 'Date'

        // // Konversi tanggal menjadi timestamp dan sortir berdasarkan tanggal
        // $timestamps = array_map('strtotime', $dates);
        // array_multisort($timestamps, SORT_ASC, $data);

        // // Harga awal dan akhir
        // $beginPrice = (float)str_replace(',', '', $data[0][5]);
        // $endPrice = (float)str_replace(',', '', end($data)[5]);

        // // Hitung jumlah tahun dari perbedaan tanggal
        // $startDate = strtotime($data[0][0]);
        // $endDate = strtotime(end($data)[0]);
        // $numYears = ($endDate - $startDate) / (365.25 * 24 * 60 * 60);

        // // Hitung CAGR
        // $cagr = ($endPrice / $beginPrice) ** (1 / $numYears) - 1;

        // return $cagr * 100; // Mengembalikan nilai dalam bentuk persentase

        
        $present = $this->cash_flow_one_year_ago($symbol);
        $past = $this->cash_flow_two_year_ago($symbol);

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
