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

        $email     = Email::where('sch_id_number', $school_id)->first();
        $satp      = Satpaccount::where('school_id', $school_id)->first();
        $kumo      = Kumosoft::where('school_id', $school_id)->first();
        $schoology = SchoologyCredential::where('school_id', $school_id)->first();

        $voucher = null;
        if (!is_null($student->voucher_id)) {
            $voucher = Voucher::find($student->voucher_id);
        }

        $courses = Course::orderBy('name')->get(['id','code','name']);

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
            'course_id'             => ['nullable','integer'],
            'status'                => ['nullable','in:0,1'],

            'birthday'              => ['nullable','date'],

            'email_address'         => ['nullable','email'],
            'email_password'        => ['nullable','string'],

            'schoology_credentials' => ['nullable','string'],

            'satp_password'         => ['nullable','string'],

            // Kumosoft (based on your kumosofts table)
            'kumosoft_school_id'    => ['nullable','string'],
            'kumosoft_password'     => ['nullable','string'],
            'kumosoft_credentials'  => ['nullable','string'],

            'voucher_code'          => ['nullable','string'],
            'free_old_voucher'      => ['nullable','boolean'],
        ]);

        $student = Student::where('school_id', $school_id)->first();
        if (!$student) {
            return $this->finishUpdateResponse($request, false, 'Student not found.', $school_id);
        }

        $changed = [];
        $rowOut  = [];

        try {
            DB::beginTransaction();

            // -------------------------
            // STUDENT: birthday
            // -------------------------
            if ($request->has('birthday')) {
                $newBday = (string) ($request->input('birthday') ?? '');
                if ($newBday !== (string) $student->birthday) {
                    $student->birthday = $newBday;
                    $student->save();
                    $changed[] = 'Birthday';
                    $rowOut['birthday'] = $student->birthday ?: '';
                }
            }

            // -------------------------
            // STUDENT: course
            // -------------------------
            if ($request->filled('course_id') && (int)$student->course_id !== (int)$request->input('course_id')) {
                $student->course_id = (int) $request->input('course_id');
                $student->save();
                $student->load('course');

                $changed[] = 'Course';
                $rowOut['course_code'] = optional($student->course)->code ?: '';
            }

            // -------------------------
            // STUDENT: status
            // -------------------------
            if ($request->has('status')) {
                $newStatus = ((string)$request->input('status') === '1') ? 1 : 0;
                if ((int)$student->status !== $newStatus) {
                    $student->status = $newStatus;
                    $student->save();

                    $changed[] = $newStatus ? 'Account Activated' : 'Account Deactivated';
                    $rowOut['status'] = $student->status;
                    $rowOut['status_text'] = $student->status ? 'Active' : 'Inactive';
                }
            }

            // -------------------------
            // EMAIL: address + password
            // - password updates only if typed
            // -------------------------
            if ($request->has('email_address') || $request->has('email_password')) {
                $email = Email::firstOrNew(['sch_id_number' => $school_id]);
                $was   = $email->exists;

                if (!$was) {
                    $email->first_name = $student->firstname ?? '';
                    $email->last_name  = $student->lastname ?? '';
                }

                if ($request->has('email_address')) {
                    $email->email_address = (string) $request->input('email_address', '');
                }

                // IMPORTANT: keep existing if blank
                if ($request->filled('email_password')) {
                    $email->password = (string) $request->input('email_password');
                }

                $email->save();

                $changed[] = $was ? 'Email (updated)' : 'Email (added)';
                $rowOut['email_address'] = $email->email_address ?? '';
            }

            // -------------------------
            // SCHOOLOGY: allow blank string to clear
            // -------------------------
            if ($request->has('schoology_credentials')) {
                $sch = SchoologyCredential::firstOrNew(['school_id' => $school_id]);
                $was = $sch->exists;

                $sch->schoology_credentials = (string) $request->input('schoology_credentials', '');
                $sch->save();

                $changed[] = $was ? 'Schoology (updated)' : 'Schoology (added)';
                $rowOut['schoology_credentials'] = $sch->schoology_credentials ?? '';
            }

            // -------------------------
            // SATP: allow blank string to clear
            // -------------------------
            if ($request->has('satp_password')) {
                $satp = Satpaccount::firstOrNew(['school_id' => $school_id]);
                $was  = $satp->exists;

                $satp->satp_password = (string) $request->input('satp_password', '');
                $satp->save();

                $changed[] = $was ? 'SATP (updated)' : 'SATP (added)';
                $rowOut['satp_password'] = $satp->satp_password ?? '';
            }

            // -------------------------
            // KUMOSOFT:
            // - Create if not exists
            // - password updates only if typed
            // - legacy can be blank string
            // -------------------------
            if ($request->has('kumosoft_school_id') || $request->has('kumosoft_password') || $request->has('kumosoft_credentials')) {
                $kumo = Kumosoft::firstOrNew(['school_id' => $school_id]);
                $was  = $kumo->exists;

                if (!$kumo->student_id) {
                    $kumo->student_id = $student->id;
                }

                if ($request->has('kumosoft_school_id')) {
                    $kumo->kumosoft_school_id = (string) $request->input('kumosoft_school_id', '');
                }

                // keep existing if blank
                if ($request->filled('kumosoft_password')) {
                    $kumo->password = (string) $request->input('kumosoft_password');
                }

                if ($request->has('kumosoft_credentials')) {
                    $kumo->kumosoft_credentials = (string) $request->input('kumosoft_credentials', '');
                }

                $kumo->save();

                $changed[] = $was ? 'Kumosoft (updated)' : 'Kumosoft (added)';
                $rowOut['kumosoft_school_id'] = $kumo->kumosoft_school_id ?? '';
                $rowOut['kumosoft_password']  = $kumo->password ?? '';
            }

            // -------------------------
            // VOUCHER: assign/unassign (old stays used)
            // -------------------------
            $voucherCode = trim((string) $request->input('voucher_code', ''));
            $freeOld     = (bool) $request->boolean('free_old_voucher');

            if ($voucherCode !== '') {
                $newVoucher = Voucher::where('voucher_code', $voucherCode)->first();
                if (!$newVoucher) {
                    DB::rollBack();
                    return $this->finishUpdateResponse($request, false, 'Voucher code not found.', $school_id);
                }

                if ((int)$newVoucher->is_given === 1 && (int)$student->voucher_id !== (int)$newVoucher->id) {
                    DB::rollBack();
                    return $this->finishUpdateResponse($request, false, 'Voucher code is already assigned to another student.', $school_id);
                }

                $student->voucher_id = $newVoucher->id;
                $student->save();

                $newVoucher->is_given = 1;
                $newVoucher->save();

                $changed[] = 'Voucher (assigned)';
                $rowOut['voucher_code'] = $newVoucher->voucher_code;
            } elseif ($freeOld) {
                if (!is_null($student->voucher_id)) {
                    $student->voucher_id = null;
                    $student->save();

                    $changed[] = 'Voucher (unassigned)';
                    $rowOut['voucher_code'] = '';
                }
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

            $newVoucher = Voucher::where('is_given', 0)->lockForUpdate()->first();
            if (!$newVoucher) {
                DB::rollBack();
                return Response::json(['success' => false, 'message' => 'No available vouchers.'], 422);
            }

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