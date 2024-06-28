<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index()
    {
        // $students = Student::all();
        // return view('students.index', compact('students'));
        $students = Student::paginate(10); // Adjust the number per page as needed
        return view('students.index', compact('students'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new StudentImport, $request->file('import_file'));

        return redirect()->back()->with('status', 'File imported successfully!');
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $courses = Course::where('status', '1')->get();
        return view('student.edit', compact('student', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'school_id' => 'required',
            'lastname' => 'required',
            'firstname' => 'required',
            'course_id' => 'required|exists:courses,id',
            'birthday' => 'required',
            'status' => 'required|boolean',
        ]);

        $student = Student::findOrFail($id);
        $student->school_id = $request->school_id;
        $student->lastname = $request->lastname;
        $student->firstname = $request->firstname;
        $student->middlename = $request->middlename;
        $student->course_id = $request->course_id;
        $student->birthday = $request->birthday;
        $student->status = $request->status;
        $student->save();

        return redirect()->route('students.index')->with('status', 'Student updated successfully!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->back()->with('status', 'Student deleted successfully!');
    }
}
