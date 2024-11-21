<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Voucher;
use App\Models\Student;
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

        // Count the total students with and without voucher codes
        $studentsWithVoucher = Student::whereNotNull('voucher_id')->count();
        $studentsWithoutVoucher = Student::whereNull('voucher_id')->count();


        // Prepare data for the voucher status pie chart
        $voucherData = [
            'labels' => ['Given', 'Not Given'],
            'values' => [$totalGiven, $totalNotGiven],
        ];

        // Prepare data for the student voucher distribution pie chart
        $studentVoucherData = [
            'labels' => ['With Voucher Code', 'Without Voucher Code'],
            'values' => [$studentsWithVoucher, $studentsWithoutVoucher],
        ];

        // Pass both data sets to the view
        return view('home', compact('voucherData', 'studentVoucherData'));
    }
}
