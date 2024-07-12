<?php

namespace App\Http\Controllers;

use App\Imports\VoucherImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VoucherController extends Controller
{
    //
    public function index()
    {
        return view('voucher.index');
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
