<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\CalculatorService;
use App\Service\YahooFinanceApiService;

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
        $symbol = $request->input('cari-nama');

        if ($symbol) {
            $calculator_service = new CalculatorService();
            $results = $calculator_service->index($symbol);
            
            return view('Pages.Calculator', [
                'results' => $results,
                'symbol' => $symbol
            ]);
        } else {
            return view('Pages.Calculator', [
                'results' => null
            ]);
        }
    }
    
}
