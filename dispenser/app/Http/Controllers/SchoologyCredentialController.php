<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoologyCredential;
use App\Imports\SchoologyCredentialImport;
use Maatwebsite\Excel\Facades\Excel;

class SchoologyCredentialController extends Controller
{
    // Show the upload form
    public function index()
    {
        return view('schoology_credentials.index');
    }

    // Store manually inputted credentials (optional)
    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required',
            'schoology_credentials' => 'required|string',
        ]);

        $existing = SchoologyCredential::where('school_id', $request->input('school_id'))->first();
        if ($existing) {
            return redirect()->back()->withErrors(['school_id' => 'This school ID already has credentials saved.']);
        }

        SchoologyCredential::create([
            'school_id' => $request->input('school_id'),
            'schoology_credentials' => $request->input('schoology_credentials'),
        ]);

        return redirect()->back()->with('status', 'Schoology credentials saved successfully!');
    }

    // Import from Excel
    public function importExcelData(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file'],
        ]);

        $import = new SchoologyCredentialImport;
        Excel::import($import, $request->file('import_file'));

        return redirect()->back()
            ->with('status', 'Excel import successful!')
            ->with('skipped', $import->skippedCount);
    }
}
