<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satpaccount;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SatpaccountImport;



class SatpController extends Controller
{
    //
    public function index(){

        return view('satpaccount.index');

    }
    public function importExcelData(Request $request)
    {
        $request->validate([
            'import_file' => [
                'required','file'
            ]

            ]);

            
            Excel::import(new SatpaccountImport, $request->file('import_file'));
        
            return redirect()->back()->with('status', 'Excel import successful!');


    }
        
}
