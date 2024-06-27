<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CallculatorController extends Controller
{
    public function index(Request $request = null)
    {
        if($request){
            $eps = $request->input('eps');
            $growth_rate = $request->input('growth_rate');
            $current_stock_price = $request->input('current_stock_price');
    
            $request_results = $this->callculator($eps, $growth_rate, $current_stock_price);
            return view('Pages.Callculator', [
                'results' => $request_results,
            ]);
        }else{
            return view('Pages.Callculator', [
                'results' => null
            ]);
        }
    }

    /**
     * Calculates the intrinsic value, current stock price, and margin of safety.
     * 
     * @return array An array containing the intrinsic value, current stock price, and margin of safety.
     */
    private function callculator($eps, $growth_rate, $current_stock_price) : array
    {
        $intrinsic_value = $eps * (8.5 + 2 * $growth_rate);
        $margin_of_safety = (($intrinsic_value - $current_stock_price) / $intrinsic_value) * 100;

        return [  
            'Intrinsik' => $growth_rate,
            'Harga' => $current_stock_price,
            'mos' => (string)$margin_of_safety.'%',
        ];
    }

}