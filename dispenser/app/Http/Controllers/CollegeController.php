<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Imports\CollegeImport;
use Maatwebsite\Excel\Facades\Excel;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::all();
        return view('college.index', compact('colleges'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new CollegeImport, $request->file('import_file'));

        return redirect()->back()->with('status', 'File imported successfully!');
    }

    public function edit($id)
    {
        $college = College::find($id);
        return view('college.edit', compact('college'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'status' => 'required|boolean',
        ]);

        $college = College::find($id);
        $college->code = $request->code;
        $college->name = $request->name;
        $college->status = $request->status;
        $college->save();

        return redirect()->back()->with('status', 'College updated successfully!');
    }

    public function destroy($id)
    {
        $college = College::find($id);
        $college->delete();

        return redirect()->back()->with('status', 'College deleted successfully!');
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

