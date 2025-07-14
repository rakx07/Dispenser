<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmailImport;
use App\Models\Email;

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
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            $import = new EmailImport();
            Excel::import($import, $request->file('import_file'));

            $message = 'Excel import successful!';
            if ($import->skipped > 0) {
                $message .= " {$import->skipped} records were skipped due to duplicate sch_id_number.";
            }

            return redirect()->back()->with('status', $message);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database Import Error: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'import_error' => 'Error importing the file: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            \Log::error('General Import Error: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'import_error' => 'Error importing the file: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for creating a new email entry.
     */
    public function create()
    {
        return view('emails.create');
    }

    /**
     * Store a newly created email entry in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email_address'  => 'required|email|unique:emails,email_address',
            'password'       => 'required|string|min:6',
            'sch_id_number'  => 'required|string|unique:emails,sch_id_number',
        ]);

        Email::create([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'email_address'  => strtolower($request->email_address),
            'password'       => $request->password,
            'sch_id_number'  => $request->sch_id_number,
        ]);

        return redirect()->route('emails.create')->with('success', 'Email entry added successfully!');
    }
}
