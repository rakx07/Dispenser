<?php

namespace App\Http\Controllers;

use App\Imports\CollegeImport;
use App\Models\College;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CollegeController extends Controller
{
    //
    public function index()
    {
        $colleges = College::all();
        return view('college.index', compact('colleges'));
    }
    public function importExcelData(Request $request)
    {
        $request -> validate([
            'import_file' =>
            'required',
            'file'

        ]);
       Excel::import(new CollegeImport, $request->file('import_file'));
        //\Maatwebsite\Excel\Facades\Excel::import(new CollegeImport, $request->file('import_file'));
        return redirect()->back()->with('status','Excel import successful!');
    }

}
