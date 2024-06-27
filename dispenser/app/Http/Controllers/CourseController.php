<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CourseImport;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('course.index', compact('courses'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new CourseImport, $request->file('import_file'));

        return redirect()->back()->with('status', 'File imported successfully!');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $departments = Department::where('status', '1')->get();
        // dd($colleges);
        return view('course.edit', compact('department', 'course'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'department_id' => 'required|exists:department,id',
            'status' => 'required|boolean',
        ]);

        $course = Department::findOrFail($id);
        $course->code = $request->code;
        $course->name = $request->name;
        $course->department_id = $request->department_id;
        //dd($course);
        $course->status = $request->status;
        $course->save();

        return redirect()->route('departments.index')->with('status', 'Department updated successfully!');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->back()->with('status', 'Course deleted successfully!');
    }

    public function importExcelData(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file',
        ]);

        Excel::import(new CourseImport, $request->file('import_file'));
        
        return redirect()->back()->with('status', 'Excel import successful!');
    }
}
