<?php

namespace App\Service;

class WaccService extends YahooFinanceApiService
{
    
    private function cost_of_equity($stock_symbol)
    {
        // Mengambil forward PE ratio dari data yang diperoleh
        $get_forward = $this->getQuote($stock_symbol);
        $forward_pe_ratio = $get_forward->forwardPE();
        
        // Menghitung biaya ekuitas
        $cost_of_equity = 1 / $forward_pe_ratio;
        
        // Mengembalikan biaya ekuitas dengan format desimal 4 angka
        return number_format($cost_of_equity, 4);
    }

    private function cost_of_debt()
    {
        // Implementasikan logika untuk menghitung biaya utang
        // Misalnya, ambil dari data yang tersedia atau hitung berdasarkan data lain
        // Contoh:
        $cost_of_debt = 0.05; // Contoh biaya utang 5%
        
        return $cost_of_debt;
    }

    private function weighting_of_capital_structure($equity, $debt)
    {
        // Menghitung struktur modal
        $total_value = $equity + $debt;
        $weight_of_equity = $equity / $total_value;
        $weight_of_debt = $debt / $total_value;
        
        return [
            'equity_weight' => $weight_of_equity,
            'debt_weight' => $weight_of_debt
        ];
    }

    public function weighted_average_cost_of_capital($stock_symbol, $equity, $debt, $taxRate) 
    {
        // Mengambil biaya ekuitas dan biaya utang dari fungsi masing-masing
        $cost_of_equity = $this->cost_of_equity($stock_symbol);
        $cost_of_debt = $this->cost_of_debt();
        
        // Mengambil struktur modal dari fungsi
        $weights = $this->weighting_of_capital_structure($equity, $debt);
        $equity_weight = $weights['equity_weight'];
        $debt_weight = $weights['debt_weight'];
        
        // Menghitung WACC
        $wacc = ($equity_weight * $cost_of_equity) + ($debt_weight * $cost_of_debt * (1 - $taxRate));
        
        // Mengembalikan nilai WACC
        return $wacc;
    }
}
?>
