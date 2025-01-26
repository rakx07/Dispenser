<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmailImport;

class EmailController extends Controller
{
    /**
     * Show the import form.
     */
    public function index()
    {
        return view('emails.index');
    }

    /**
     * Handle the Excel file upload and import process.
     */
    public function importExcelData(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            // Import the Excel file
            Excel::import(new EmailImport, $request->file('import_file'));

            return redirect()->back()->with('status', 'Excel import successful!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['import_error' => 'There was an error importing the file. Please ensure the format is correct.']);
        }
    }
}
