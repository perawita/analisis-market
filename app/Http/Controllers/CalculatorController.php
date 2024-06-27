<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    /**
     * Handles the calculator result request.
     *
     * @param Request|null $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $eps = $request->input('eps');
        $growth_rate = $request->input('growth_rate');
        $current_stock_price = $request->input('current_stock_price');

        if ($eps && $growth_rate && $current_stock_price) {
            $request_results = $this->calculate($eps, $growth_rate, $current_stock_price);
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
            'mos' => (string)$margin_of_safety.'%',
        ];
    }
}
