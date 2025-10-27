<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
        $q      = trim((string) $request->query('q', ''));
        $course = trim((string) $request->query('course', ''));
        $onlyWithCreds = (bool) $request->query('only_with_creds', false);

        $studentsQuery = Student::query()
            ->with(['course'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('school_id', 'like', "%{$q}%")
                      ->orWhere('firstname', 'like', "%{$q}%")
                      ->orWhere('lastname', 'like', "%{$q}%")
                      ->orWhere('middlename', 'like', "%{$q}%");
                });
            })
            // keep course filter by CODE (input remains, just no helper text)
            ->when($course !== '', function ($qq) use ($course) {
                $qq->whereHas('course', function ($c) use ($course) {
                    $c->where('code', $course);
                });
            })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->limit(100);

        $students = $studentsQuery->get();

        $schoolIds  = $students->pluck('school_id')->filter()->values();
        $voucherIds = $students->pluck('voucher_id')->filter()->values();

        $emails     = Email::whereIn('sch_id_number', $schoolIds)->get()->keyBy('sch_id_number');
        $satps      = Satpaccount::whereIn('school_id', $schoolIds)->get()->keyBy('school_id');
        $kumos      = Kumosoft::whereIn('school_id', $schoolIds)->get()->keyBy('school_id');
        $schoologys = SchoologyCredential::whereIn('school_id', $schoolIds)->get()->keyBy('school_id');
        $vouchers   = $voucherIds->isNotEmpty()
            ? Voucher::whereIn('id', $voucherIds)->get()->keyBy('id')
            : collect();

        if ($onlyWithCreds) {
            $students = $students->filter(function ($s) use ($emails, $satps, $kumos, $schoologys, $vouchers) {
                return $emails->has($s->school_id)
                    || $satps->has($s->school_id)
                    || $kumos->has($s->school_id)
                    || $schoologys->has($s->school_id)
                    || ($s->voucher_id && $vouchers->has($s->voucher_id));
            })->values();
        }

        $autoOpenId = null;
        if ($q !== '' && $students->count() === 1) {
            $autoOpenId = $students->first()->school_id;
        }

        $courses = Course::orderBy('name')->get(['id', 'code', 'name']);

        return view('filters.filter', [
            'q'           => $q,
            'course'      => $course,
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

    public function update(Request $request, string $school_id)
    {
        $request->validate([
            'email_address'          => ['nullable', 'email'],
            'email_password'         => ['nullable', 'string'],
            'satp_password'          => ['nullable', 'string'],
            'schoology_credentials'  => ['nullable', 'string'],
            'kumosoft_credentials'   => ['nullable', 'string'],
            'voucher_code'           => ['nullable', 'string'],
            'free_old_voucher'       => ['nullable', 'boolean'],
            'birthday'               => ['nullable', 'date'], // NEW
        ]);

        $student = Student::where('school_id', $school_id)->first();
        if (!$student) {
            return back()->withErrors(['student' => 'Student not found for School ID: '.$school_id])->withInput();
        }

        $changed = [];

        DB::beginTransaction();
        try {
            // ===== EMAIL =====
            if ($request->hasAny(['email_address', 'email_password'])) {
                $email = Email::firstOrNew(['sch_id_number' => $school_id]);

                $addr = $request->input('email_address');
                $pass = $request->input('email_password');

                if (!$email->exists && ($addr === null && $pass === null)) {
                    // skip
                } else {
                    if ($addr === null && $email->exists) $addr = '';
                    if ($pass === null && $email->exists) $pass = '';

                    if ($addr === null) $addr = $email->email_address;
                    if ($pass === null) $pass = $email->password;

                    $email->email_address = $addr;
                    $email->password      = $pass;
                    $email->save();

                    if ($email->wasRecentlyCreated || $email->wasChanged(['email_address','password'])) {
                        $changed[] = 'Email';
                    }
                }
            }

            // ===== SATP =====
            if ($request->has('satp_password')) {
                $satp = Satpaccount::firstOrNew(['school_id' => $school_id]);
                $val  = $request->input('satp_password');

                if (!$satp->exists && $val === null) {
                    // skip
                } else {
                    if ($val === null && $satp->exists) $val = '';
                    if ($val === null) $val = $satp->satp_password;
                    $satp->satp_password = $val;
                    $satp->save();

                    if ($satp->wasRecentlyCreated || $satp->wasChanged('satp_password')) {
                        $changed[] = 'SATP';
                    }
                }
            }

            // ===== SCHOOLOGY =====
            if ($request->has('schoology_credentials')) {
                $sch = SchoologyCredential::firstOrNew(['school_id' => $school_id]);
                $val = $request->input('schoology_credentials');

                if (!$sch->exists && $val === null) {
                    // skip
                } else {
                    if ($val === null && $sch->exists) $val = '';
                    if ($val === null) $val = $sch->schoology_credentials;
                    $sch->schoology_credentials = $val;
                    $sch->save();

                    if ($sch->wasRecentlyCreated || $sch->wasChanged('schoology_credentials')) {
                        $changed[] = 'Schoology';
                    }
                }
            }

            // ===== KUMOSOFT =====
            if ($request->has('kumosoft_credentials')) {
                $ks  = Kumosoft::firstOrNew(['school_id' => $school_id]);
                $val = $request->input('kumosoft_credentials');

                if (!$ks->exists && $val === null) {
                    // skip
                } else {
                    if ($val === null && $ks->exists) $val = '';
                    if ($val === null) $val = $ks->kumosoft_credentials;
                    $ks->kumosoft_credentials = $val;
                    $ks->save();

                    if ($ks->wasRecentlyCreated || $ks->wasChanged('kumosoft_credentials')) {
                        $changed[] = 'Kumosoft';
                    }
                }
            }

            // ===== BIRTHDAY (Student) =====
            if ($request->has('birthday')) {
                $newBday = $request->input('birthday'); // nullable (empty -> null)
                if ($newBday !== null && $newBday !== '') {
                    $student->birthday = $newBday;
                    $student->save();
                    if ($student->wasChanged('birthday')) {
                        $changed[] = 'Birthday';
                    }
                }
            }

            // ===== VOUCHER (manual entry path) =====
            $voucherCode = trim((string) $request->input('voucher_code', ''));
            $freeOld     = (bool) $request->boolean('free_old_voucher');

            if ($voucherCode !== '') {
                $newVoucher = Voucher::where('voucher_code', $voucherCode)->first();
                if (!$newVoucher) {
                    DB::rollBack();
                    return back()->withErrors(['voucher_code' => 'Voucher code not found.'])->withInput();
                }
                if ((int) $newVoucher->is_given === 1 && (int) $student->voucher_id !== (int) $newVoucher->id) {
                    DB::rollBack();
                    return back()->withErrors(['voucher_code' => 'Voucher code is already assigned to someone else.'])->withInput();
                }

                if (!is_null($student->voucher_id) && (int)$student->voucher_id !== (int)$newVoucher->id) {
                    Voucher::where('id', $student->voucher_id)->update(['is_given' => 0]);
                }

                $student->voucher_id = $newVoucher->id;
                $student->save();

                $newVoucher->is_given = 1;
                $newVoucher->save();

                $changed[] = 'Voucher (assigned)';
            } elseif ($freeOld) {
                if (!is_null($student->voucher_id)) {
                    Voucher::where('id', $student->voucher_id)->update(['is_given' => 0]);
                    $student->voucher_id = null;
                    $student->save();
                    $changed[] = 'Voucher (unassigned)';
                }
            }

            DB::commit();

            $changedText = empty($changed) ? 'No fields changed' : implode(', ', $changed);

            return redirect()
                ->route('filters.index', ['q' => $school_id])
                ->with('status', 'Credentials updated successfully.')
                ->with('changed', $changed)
                ->with('changed_text', $changedText)
                ->with('auto_open', $school_id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['server' => 'Error: '.$e->getMessage()])->withInput();
        }
    }

    public function generateVoucher(Request $request, string $school_id): JsonResponse
    {
        $student = Student::where('school_id', $school_id)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        DB::beginTransaction();
        try {
            $newVoucher = Voucher::where('is_given', 0)->first();
            if (!$newVoucher) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'No available vouchers to assign.'], 422);
            }

            if (!is_null($student->voucher_id)) {
                Voucher::where('id', $student->voucher_id)->update(['is_given' => 0]);
            }

            $student->voucher_id = $newVoucher->id;
            $student->save();

            $newVoucher->is_given = 1;
            $newVoucher->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'voucher_code' => $newVoucher->voucher_code,
                'message' => 'Voucher generated and assigned.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }
}
