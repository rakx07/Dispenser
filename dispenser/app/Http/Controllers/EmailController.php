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
    $request->validate([
        'import_file' => 'required|file|mimes:xlsx,csv',
    ]);

    try {
        $import = new EmailImport();
        Excel::import($import, $request->file('import_file'));

        $message = 'Excel import successful!';
        if ($import->skipped > 0) {
            $message .= " {$import->skipped} records were skipped due to duplicates or missing data.";
        }

        return redirect()->back()->with('status', $message);
    } catch (\Illuminate\Database\QueryException $e) {
        // Check if the error is a duplicate entry error
        if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
            preg_match("/Duplicate entry '(.*?)' for key/", $e->getMessage(), $matches);
            $duplicateValue = $matches[1] ?? 'unknown';

            return redirect()->back()->withErrors([
                'import_error' => "Error: Duplicate entry found for `sch_id_number`: {$duplicateValue}.",
            ]);
        }

        // Handle other query exceptions
        return redirect()->back()->withErrors([
            'import_error' => 'Error importing the file: ' . $e->getMessage(),
        ]);
    } catch (\Exception $e) {
        return redirect()->back()->withErrors([
            'import_error' => 'Error importing the file: ' . $e->getMessage(),
        ]);
    }
}


}
