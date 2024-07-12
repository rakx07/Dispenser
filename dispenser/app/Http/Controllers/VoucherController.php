<?php

namespace App\Http\Controllers;

use App\Imports\VoucherImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Voucher;

class VoucherController extends Controller
{
    //
    public function index()
    {
        $vouchers = Voucher::paginate(20);
    return view('voucher.index', compact('vouchers'));
    }
    public function importExcelData(Request $request)
    {
            $request->validate(
                [
                    'import_file'=> [
                        'required',
                        'file',
                    ]
                ]
                );
                Excel::import(new VoucherImport, $request->file('import_file'));
        //\Maatwebsite\Excel\Facades\Excel::import(new CollegeImport, $request->file('import_file'));
        return redirect()->back()->with('status','Excel import successful!');
    }
}
