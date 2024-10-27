<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satpaccount;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SatpaccountImport;

class SatpController extends Controller
{
    // Show the SATP account index page
    public function index()
    {
        return view('satpaccount.index');
    }

    // Import SATP accounts from Excel
    public function importExcelData(Request $request)
{
    $request->validate([
        'import_file' => ['required', 'file']
    ]);

    // Import the data using Maatwebsite Excel
    $import = new SatpaccountImport;
    Excel::import($import, $request->file('import_file'));

    // Get the count of skipped entries
    $skippedCount = $import->skippedCount;

    // Redirect back with a success message and skipped count
    return redirect()->back()->with('status', 'Excel import successful! Skipped ' . $skippedCount . ' duplicate(s).');
}

    // Show the form to create a new SATP account
    public function create()
    {
        return view('satpaccount.create');
    }

    // Store a new SATP account in the database with duplicate check
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'school_id' => 'required',
            'satp_password' => 'required',
        ]);

        // Check if a SATP account with the same student_id already exists
        $existingAccount = Satpaccount::where('school_id', $request->input('school_id'))->first();

        if ($existingAccount) {
            // If a duplicate student_id is found, return an error message
            return redirect()->back()->withErrors(['school_id' => 'This student ID already exists!']);
        }

        // Create a new SATP account with clear-text password if no duplicate is found
        Satpaccount::create([
            'school_id' => $request->input('school_id'),
            'satp_password' => $request->input('satp_password'),
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('status', 'SATP account created successfully!');
    }
}
