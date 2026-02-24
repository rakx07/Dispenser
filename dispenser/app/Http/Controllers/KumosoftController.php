<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Kumosoft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KumosoftFailedRowsExport;
use App\Exports\KumosoftTemplateExport;

class KumosoftController extends Controller
{
    /**
     * Suffix tokens used to clean name comparisons
     */
    private array $suffixTokens = ['JR', 'SR', 'II', 'III', 'IV'];

    /**
     * GET /kumosoft/import
     * Shows the import page
     */
    public function index()
    {
        return view('kumosoft.index'); // resources/views/kumosoft/import.blade.php
    }

public function downloadTemplate()
{
    $filename = 'kumosoft_upload_template_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(new KumosoftTemplateExport(), $filename);
}


    /**
     * GET /kumosoft/create
     * Manual entry form (keep your existing view if you have one)
     */
    public function create()
    {
        return view('kumosoft.create'); // if you have it
    }

    /**
     * POST /kumosoft/store
     * Manual store (basic)
     */
    public function store(Request $request)
    {
        $request->validate([
            'eis_school_id' => ['required', 'string'],
            'kumosoft_school_id' => ['nullable', 'string'],
            'lastname' => ['nullable', 'string'],
            'firstname' => ['nullable', 'string'],
            'middlename' => ['nullable', 'string'],
            'suffix' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'username' => ['nullable', 'string'],
            'password' => ['nullable', 'string'],
        ]);

        $student = Student::where('school_id', $request->eis_school_id)->first();

        // If student exists, we’ll attach student_id; if not, still allow saving if you want.
        // If you want to block manual save when student not found, tell me.
        $data = [
            'school_id'            => $request->eis_school_id, // keep legacy column compatible
            'kumosoft_credentials' => $request->username . ':' . $request->password,

            'student_id'           => $student?->id,
            'eis_school_id'        => $request->eis_school_id,
            'kumosoft_school_id'   => $request->kumosoft_school_id,

            'lastname'             => $request->lastname,
            'firstname'            => $request->firstname,
            'middlename'           => $request->middlename,
            'suffix'               => $request->suffix,
            'email'                => $request->email,
            'username'             => $request->username,
            'password'             => $request->password,

            'match_status'         => 'MANUAL',
            'match_reason'         => 'Manually added by admin.',
            'matched_at'           => now(),
        ];

        Kumosoft::updateOrCreate(
            ['eis_school_id' => $request->eis_school_id],
            $data
        );

        return back()->with('success', 'Kumosoft record saved.');
    }

    /**
     * POST /kumosoft/import
     * Batch 2: Import Excel with matching rules + failed reasons export.
     */
    public function importExcelData(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        // Read first sheet into collection
        $sheets = Excel::toCollection(null, $request->file('file'));
        $rows = $sheets->first() ?? collect();

        if ($rows->isEmpty()) {
            return back()->with('error', 'Uploaded file is empty.');
        }

        // Detect if associative rows or header row
        $first = $rows->first();
        $isAssociative = is_array($first) && array_keys($first) !== range(0, count($first) - 1);

        if (!$isAssociative) {
            // Assume first row is headings
            $header = $rows->shift();
            $header = collect($header)->map(fn($h) => strtolower(trim((string)$h)))->toArray();

            $rows = $rows->map(function ($r) use ($header) {
                $arr = [];
                foreach ($header as $i => $key) {
                    $arr[$key] = $r[$i] ?? null;
                }
                return $arr;
            });
        } else {
            // Make keys lowercase
            $rows = $rows->map(function ($r) {
                $out = [];
                foreach ($r as $k => $v) {
                    $out[strtolower(trim((string)$k))] = $v;
                }
                return $out;
            });
        }

        // Required headers
        $required = ['school_id','lastname','firstname','middlename','suffix','email','username','password'];
        $missing = array_values(array_diff($required, array_keys((array)$rows->first())));
        if (!empty($missing)) {
            return back()->with('error', 'Missing required column(s): ' . implode(', ', $missing));
        }

        // Track duplicates (Excel school_id)
        $seenExcelSchoolIds = [];
        $failed = [];
        $stats = [
            'total_rows' => 0,
            'kept_rows' => 0,
            'matched_by_id' => 0,
            'matched_by_name' => 0,
            'failed' => 0,
            'duplicates_in_upload' => 0,
        ];

        // Load students for matching
        $students = Student::select('id', 'school_id', 'lastname', 'firstname', 'middlename')->get();

        $studentsBySchoolId = $students->keyBy(fn($s) => (string)$s->school_id);

        $studentsByNameKey = [];
        foreach ($students as $s) {
            $ln = $this->normalizeName((string)$s->lastname, true);
            $fn = $this->normalizeName((string)$s->firstname, true);
            $key = $ln . '|' . $fn;
            $studentsByNameKey[$key][] = $s;
        }

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $stats['total_rows']++;

                $excelSchoolId = trim((string)($row['school_id'] ?? ''));

                // Skip blank rows
                if ($excelSchoolId === '' &&
                    trim((string)($row['lastname'] ?? '')) === '' &&
                    trim((string)($row['firstname'] ?? '')) === ''
                ) {
                    continue;
                }

                // Duplicate check (Excel school_id)
                if ($excelSchoolId !== '' && isset($seenExcelSchoolIds[$excelSchoolId])) {
                    $stats['duplicates_in_upload']++;
                    $failed[] = $this->failedRow($row, 'DUPLICATE_IN_UPLOAD', 'Duplicate school_id in uploaded file (kept the first occurrence).');
                    continue;
                }
                if ($excelSchoolId !== '') {
                    $seenExcelSchoolIds[$excelSchoolId] = true;
                }

                $stats['kept_rows']++;

                // Normalize Excel names
                $excelLN = $this->normalizeName((string)$row['lastname'], true);
                $excelFN = $this->normalizeName((string)$row['firstname'], true);

                // 1) Match by ID
                $student = $excelSchoolId !== '' ? ($studentsBySchoolId[$excelSchoolId] ?? null) : null;

                if ($student) {
                    // If IDs match but names mismatch => do NOT save
                    $studLN = $this->normalizeName((string)$student->lastname, true);
                    $studFN = $this->normalizeName((string)$student->firstname, true);

                    if ($studLN !== $excelLN || $studFN !== $excelFN) {
                        $failed[] = $this->failedRow(
                            $row,
                            'NAME_MISMATCH_FOR_SAME_ID',
                            'School ID matched a student, but lastname/firstname did not match after normalization.'
                        );
                        continue;
                    }

                    $this->upsertKumosoft($student, $row, 'MATCHED_BY_ID', 'Matched by school_id and name.');
                    $stats['matched_by_id']++;
                    continue;
                }

                // 2) Match by Name (lastname + firstname only)
                $nameKey = $excelLN . '|' . $excelFN;
                $candidates = $studentsByNameKey[$nameKey] ?? [];

                if (count($candidates) === 1) {
                    $student = $candidates[0];

                    // Save mapping (IDs differ allowed; persona same by name)
                    $this->upsertKumosoft($student, $row, 'MATCHED_BY_NAME', 'Matched by lastname+firstname (normalized), school_id differs.');
                    $stats['matched_by_name']++;
                    continue;
                }

                if (count($candidates) > 1) {
                    $failed[] = $this->failedRow(
                        $row,
                        'AMBIGUOUS_NAME_MATCH',
                        'Multiple students share the same lastname+firstname. Cannot safely match.'
                    );
                    continue;
                }

                // 3) Not found
                $failed[] = $this->failedRow(
                    $row,
                    'NOT_FOUND_IN_STUDENTS',
                    'No matching student found by school_id or lastname+firstname.'
                );
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $stats['failed'] = count($failed);

        // Store failed report file
        $failedFile = null;
        if (!empty($failed)) {
            $failedFile = 'exports/kumosoft_failed_' . now()->format('Ymd_His') . '.xlsx';
            Excel::store(new KumosoftFailedRowsExport($failed), $failedFile, 'local');
        }

        return back()->with([
            'success' => 'Kumosoft import completed.',
            'kumosoft_import_stats' => $stats,
            'kumosoft_failed_file' => $failedFile,
        ]);
    }

    /**
     * GET /kumosoft/import/failed/{filename}
     */
    public function downloadFailed($filename)
    {
        $filename = basename($filename);
        $path = 'exports/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download($path);
    }

    /* =========================
     * Helpers
     * ========================= */

    private function upsertKumosoft(Student $student, array $row, string $status, string $reason): void
    {
        $excelSchoolId = trim((string)($row['school_id'] ?? ''));

        $data = [
            // Legacy columns (keep system stable)
            'school_id'            => (string)$student->school_id,
            'kumosoft_credentials' => $this->legacyCredentialsString($row),

            // New columns
            'student_id'          => $student->id,
            'eis_school_id'       => (string)$student->school_id,
            'kumosoft_school_id'  => $excelSchoolId !== '' ? $excelSchoolId : null,

            'lastname'            => trim((string)($row['lastname'] ?? '')),
            'firstname'           => trim((string)($row['firstname'] ?? '')),
            'middlename'          => trim((string)($row['middlename'] ?? '')),
            'suffix'              => trim((string)($row['suffix'] ?? '')),
            'email'               => trim((string)($row['email'] ?? '')),
            'username'            => trim((string)($row['username'] ?? '')),
            'password'            => trim((string)($row['password'] ?? '')),

            'match_status'        => $status,
            'match_reason'        => $reason,
            'matched_at'          => now(),
        ];

        // 1 record per student (eis_school_id unique)
        Kumosoft::updateOrCreate(
            ['eis_school_id' => (string)$student->school_id],
            $data
        );
    }

    private function legacyCredentialsString(array $row): string
    {
        $u = trim((string)($row['username'] ?? ''));
        $p = trim((string)($row['password'] ?? ''));
        if ($u === '' && $p === '') return '-';
        return $u . ':' . $p;
    }

    private function failedRow(array $row, string $code, string $message): array
    {
        return [
            'school_id'  => $row['school_id'] ?? null,
            'lastname'   => $row['lastname'] ?? null,
            'firstname'  => $row['firstname'] ?? null,
            'middlename' => $row['middlename'] ?? null,
            'suffix'     => $row['suffix'] ?? null,
            'email'      => $row['email'] ?? null,
            'username'   => $row['username'] ?? null,
            'password'   => $row['password'] ?? null,
            'error_code' => $code,
            'error_msg'  => $message,
        ];
    }

    private function normalizeName(string $value, bool $stripSuffixAtEnd): string
    {
        $v = strtoupper(trim($value));
        $v = preg_replace('/[.,]/', '', $v);
        $v = preg_replace('/\s+/', ' ', $v);

        if ($stripSuffixAtEnd) {
            $v = $this->stripSuffixTokenAtEnd($v);
        }

        return trim($v);
    }

    private function stripSuffixTokenAtEnd(string $value): string
    {
        $v = trim($value);
        if ($v === '') return $v;

        $parts = explode(' ', $v);
        $last = end($parts);

        if ($last !== false && in_array($last, $this->suffixTokens, true)) {
            array_pop($parts);
            return trim(implode(' ', $parts));
        }

        return $v;
    }
}