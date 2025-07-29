<?php

// app/Http/Controllers/KumosoftController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kumosoft;
use App\Imports\KumosoftImport;
use Maatwebsite\Excel\Facades\Excel;

class KumosoftController extends Controller
{
    public function index()
    {
        return view('kumosoft.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|string',
            'kumosoft_credentials' => 'required|string',
        ]);

        if (Kumosoft::where('school_id', $request->school_id)->exists()) {
            return redirect()->back()->withErrors(['school_id' => 'This school ID already exists.']);
        }

        Kumosoft::create($request->only('school_id', 'kumosoft_credentials'));

        return back()->with('status', 'Kumosoft credentials saved successfully!');
    }

    public function importExcelData(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new KumosoftImport;
        Excel::import($import, $request->file('import_file'));

        return back()
            ->with('status', 'Excel import successful!')
            ->with('skipped', $import->skippedCount);
    }

    public function create()
    {
        return view('kumosoft.create');
    }
}
