<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Imports\DepartmentImport;
use Maatwebsite\Excel\Facades\Excel;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('department.index', compact('departments'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new DepartmentImport, $request->file('import_file'));

        return redirect()->back()->with('status', 'File imported successfully!');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $colleges = College::where('status', '1')->get();
        // dd($colleges);
        return view('department.edit', compact('department', 'colleges'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'college_id' => 'required|exists:colleges,id',
            'status' => 'required|boolean',
        ]);

        $department = Department::findOrFail($id);
        $department->code = $request->code;
        $department->name = $request->name;
        $department->college_id = $request->college_id;
        //dd($department);
        $department->status = $request->status;
        $department->save();

        return redirect()->route('departments.index')->with('status', 'Department updated successfully!');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()->back()->with('status', 'Department deleted successfully!');
    }

    public function importExcelData(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file',
        ]);

        Excel::import(new DepartmentImport, $request->file('import_file'));
        
        return redirect()->back()->with('status', 'Excel import successful!');
    }
}
