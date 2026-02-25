<?php
// app/Http/Controllers/FilterController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Student;
use App\Models\Course;
use App\Models\Voucher;
use App\Models\Email;
use App\Models\Satpaccount;
use App\Models\Kumosoft;
use App\Models\SchoologyCredential;

class FilterController extends Controller
{
    public function index(Request $request)
    {
        $q             = trim((string) $request->query('q', ''));
        $courseCode    = trim((string) $request->query('course', ''));
        $onlyWithCreds = (bool) $request->boolean('only_with_creds', false);
        $perPage       = (int) ($request->query('per_page', 15)) ?: 15;

        /** @var LengthAwarePaginator $students */
        $students = Student::query()
            ->with(['course'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('school_id', 'like', "%{$q}%")
                      ->orWhere('firstname', 'like', "%{$q}%")
                      ->orWhere('lastname',  'like', "%{$q}%")
                      ->orWhere('middlename','like', "%{$q}%");
                });
            })
            ->when($courseCode !== '', function ($qq) use ($courseCode) {
                $qq->whereHas('course', function ($c) use ($courseCode) {
                    $c->where('code', $courseCode);
                });
            })
            ->orderBy('lastname')->orderBy('firstname')
            ->paginate($perPage);

        // Compat for hybrid Laravel installs
        if (method_exists($students, 'withQueryString')) {
            $students = $students->withQueryString();
        } else {
            $students->appends($request->query());
        }

        // Load related creds in one shot
        $schoolIds  = $students->getCollection()->pluck('school_id')->filter()->values();
        $voucherIds = $students->getCollection()->pluck('voucher_id')->filter()->values();

        $emails     = $schoolIds->isNotEmpty()
            ? Email::whereIn('sch_id_number', $schoolIds)->get()->keyBy('sch_id_number')
            : collect();

        $satps      = $schoolIds->isNotEmpty()
            ? Satpaccount::whereIn('school_id', $schoolIds)->get()->keyBy('school_id')
            : collect();

        $kumos      = $schoolIds->isNotEmpty()
            ? Kumosoft::whereIn('school_id', $schoolIds)->get()->keyBy('school_id')
            : collect();

        $schoologys = $schoolIds->isNotEmpty()
            ? SchoologyCredential::whereIn('school_id', $schoolIds)->get()->keyBy('school_id')
            : collect();

        $vouchers   = $voucherIds->isNotEmpty()
            ? Voucher::whereIn('id', $voucherIds)->get()->keyBy('id')
            : collect();

        if ($onlyWithCreds) {
            $filtered = $students->getCollection()->filter(function ($s) use ($emails, $satps, $kumos, $schoologys, $vouchers) {
                return $emails->has($s->school_id)
                    || $satps->has($s->school_id)
                    || $kumos->has($s->school_id)
                    || $schoologys->has($s->school_id)
                    || ($s->voucher_id && $vouchers->has($s->voucher_id));
            })->values();
            $students->setCollection($filtered);
        }

        // Auto-open when exactly one result
        $autoOpenId = null;
        if ($q !== '' && $students->total() === 1) {
            $only = $students->getCollection()->first();
            if ($only) $autoOpenId = $only->school_id;
        }

        $courses = Course::orderBy('name')->get(['id','code','name']);

        return view('filters.filter', [
            'q'           => $q,
            'course'      => $courseCode,
            'onlyWith'    => $onlyWithCreds,
            'students'    => $students,
            'courses'     => $courses,
            'emails'      => $emails,
            'satps'       => $satps,
            'kumos'       => $kumos,
            'schoologys'  => $schoologys,
            'vouchers'    => $vouchers,
            'autoOpenId'  => $autoOpenId,
        ]);
    }

    public function edit(string $school_id)
    {
        $student = Student::with('course')->where('school_id', $school_id)->firstOrFail();

        // Preload related credentials for the form
        $email     = Email::where('sch_id_number', $school_id)->first();
        $satp      = Satpaccount::where('school_id', $school_id)->first();
        $kumo      = Kumosoft::where('school_id', $school_id)->first();
        $schoology = SchoologyCredential::where('school_id', $school_id)->first();

        $voucher = null;
        if (!is_null($student->voucher_id)) {
            $voucher = Voucher::find($student->voucher_id);
        }

        $courses = Course::orderBy('name')->get(['id','code','name']); // for dropdown

        return view('filters.edit', [
            'student'   => $student,
            'email'     => $email,
            'satp'      => $satp,
            'kumo'      => $kumo,
            'schoology' => $schoology,
            'voucher'   => $voucher,
            'courses'   => $courses,
        ]);
    }

   public function update(Request $request, string $school_id)
{
    $request->validate([
        'email_address'         => ['nullable','email'],
        'email_password'        => ['nullable','string'],
        'satp_password'         => ['nullable','string'],
        'schoology_credentials' => ['nullable','string'],

        // ✅ Kumosoft fields
        'kumosoft_school_id'    => ['nullable','string'],
        'kumosoft_password'     => ['nullable','string'],
        'kumosoft_credentials'  => ['nullable','string'],

        'voucher_code'          => ['nullable','string'],
        'birthday'              => ['nullable','date'],
        'free_old_voucher'      => ['nullable','boolean'],
        'course_id'             => ['nullable','integer'],
        'status'                => ['nullable','boolean'],
    ]);

    $student = Student::where('school_id', $school_id)->first();
    if (!$student) {
        return $this->finishUpdateResponse($request, false, 'Student not found.', $school_id);
    }

    $changed = [];
    $rowOut  = [];

    try {
        DB::beginTransaction();

        /*
        |--------------------------------------------------------------------------
        | Kumosoft (FULL UPSERT LOGIC)
        |--------------------------------------------------------------------------
        | - Create if not exists
        | - Update only fields sent
        | - Keep password if blank
        */
        if (
            $request->has('kumosoft_school_id') ||
            $request->has('kumosoft_password') ||
            $request->has('kumosoft_credentials')
        ) {
            $kumo = Kumosoft::firstOrNew(['school_id' => $school_id]);
            $was  = $kumo->exists;

            // Always ensure link to student_id if available
            if (!$kumo->student_id) {
                $kumo->student_id = $student->id;
            }

            // Update Kumosoft School ID
            if ($request->has('kumosoft_school_id')) {
                $kumo->kumosoft_school_id = (string) $request->input('kumosoft_school_id', '');
            }

            // Update password ONLY if not empty
            if ($request->filled('kumosoft_password')) {
                $kumo->password = (string) $request->input('kumosoft_password');
            }

            // Legacy credentials
            if ($request->has('kumosoft_credentials')) {
                $kumo->kumosoft_credentials = (string) $request->input('kumosoft_credentials', '');
            }

            $kumo->save();

            $changed[] = $was ? 'Kumosoft (updated)' : 'Kumosoft (added)';

            $rowOut['kumosoft_school_id'] = $kumo->kumosoft_school_id ?? '';
            $rowOut['kumosoft_password']  = $kumo->password ?? '';
            $rowOut['kumosoft_credentials'] = $kumo->kumosoft_credentials ?? '';
        }

        DB::commit();

        $changed = array_values(array_unique($changed));
        $changedText = empty($changed) ? 'No fields changed' : implode(', ', $changed);

        if ($request->ajax() || $request->wantsJson()) {
            return Response::json([
                'success'      => true,
                'message'      => 'Changes saved.',
                'changed'      => $changed,
                'changed_text' => $changedText,
                'row'          => $rowOut,
            ]);
        }

        return redirect()
            ->route('filters.index', ['q' => $school_id])
            ->with('status', 'Changes saved.')
            ->with('changed', $changed)
            ->with('changed_text', $changedText)
            ->with('auto_open', $school_id);

    } catch (\Throwable $e) {
        DB::rollBack();
        return $this->finishUpdateResponse($request, false, 'Error: '.$e->getMessage(), $school_id);
    }
}

    private function finishUpdateResponse(Request $request, bool $ok, string $msg, string $school_id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return Response::json(['success' => $ok, 'message' => $msg], $ok ? 200 : 422);
        }
        return back()->withErrors(['server' => $msg])->withInput();
    }

    public function generateVoucher(Request $request, string $school_id)
    {
        $student = Student::where('school_id', $school_id)->first();
        if (!$student) {
            return Response::json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        try {
            DB::beginTransaction();

            // Pick the next available (unused) voucher
            $newVoucher = Voucher::where('is_given', 0)->lockForUpdate()->first();
            if (!$newVoucher) {
                DB::rollBack();
                return Response::json(['success' => false, 'message' => 'No available vouchers.'], 422);
            }

            // DO NOT free the student's old voucher if any. Old remains used forever.
            $student->voucher_id = $newVoucher->id;
            $student->save();

            $newVoucher->is_given = 1;
            $newVoucher->save();

            DB::commit();

            return Response::json([
                'success'      => true,
                'voucher_code' => $newVoucher->voucher_code,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return Response::json(['success' => false, 'message' => 'Server error: '.$e->getMessage()], 500);
        }
    }
}
