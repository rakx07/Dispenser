<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Voucher;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
 // Count the total given and not given vouchers
 $totalGiven = Voucher::where('is_given', 1)->count();
 $totalNotGiven = Voucher::where('is_given', 0)->count();

 // Prepare data for the pie chart
 $data = [
     'labels' => ['Given', 'Not Given'],
     'values' => [$totalGiven, $totalNotGiven]
 ];

 return view('home', compact('data'));

        // return view('home');
    }
}
