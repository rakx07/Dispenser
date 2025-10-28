<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    /**
     * Search & list page.
     */
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
            ->when($course !== '', function ($qq) use ($course) {
                $qq->whereHas('course', function ($c) use ($course) {
                    $c->where('code', $course);
                });
            })
            // ✅ push "only with credentials" into SQL using EXISTS
            ->when($onlyWithCreds, function ($qq) {
                $qq->where(function ($w) {
                    $w->whereNotNull('voucher_id')
                      ->orWhereExists(function ($sub) {
                          $sub->select(DB::raw(1))
                              ->from('emails')
                              ->whereColumn('emails.sch_id_number', 'students.school_id');
                      })
                      ->orWhereExists(function ($sub) {
                          $sub->select(DB::raw(1))
                              ->from('satpaccounts')
                              ->whereColumn('satpaccounts.school_id', 'students.school_id');
                      })
                      ->orWhereExists(function ($sub) {
                          $sub->select(DB::raw(1))
                              ->from('kumosofts')
                              ->whereColumn('kumosofts.school_id', 'students.school_id');
                      })
                      ->orWhereExists(function ($sub) {
                          $sub->select(DB::raw(1))
                              ->from('schoology_credentials')
                              ->whereColumn('schoology_credentials.school_id', 'students.school_id');
                      });
                });
            })
            ->orderBy('lastname')
            ->orderBy('firstname');

        // Paginate (15)
        $students = $studentsQuery->paginate(15);

        // Eager related credential records for the current page
        $pageItems  = collect($students->items());
        $schoolIds  = $pageItems->pluck('school_id')->filter()->values();
        $voucherIds = $pageItems->pluck('voucher_id')->filter()->values();

        $emails     = $schoolIds->isNotEmpty() ? Email::whereIn('sch_id_number', $schoolIds)->get()->keyBy('sch_id_number') : collect();
        $satps      = $schoolIds->isNotEmpty() ? Satpaccount::whereIn('school_id', $schoolIds)->get()->keyBy('school_id')       : collect();
        $kumos      = $schoolIds->isNotEmpty() ? Kumosoft::whereIn('school_id', $schoolIds)->get()->keyBy('school_id')          : collect();
        $schoologys = $schoolIds->isNotEmpty() ? SchoologyCredential::whereIn('school_id', $schoolIds)->get()->keyBy('school_id'): collect();
        $vouchers   = $voucherIds->isNotEmpty() ? Voucher::whereIn('id', $voucherIds)->get()->keyBy('id') : collect();

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
        ]);
    }

    /**
     * AJAX voucher generator: POST /filters/{school_id}/voucher/generate
     */
    public function generateVoucher(Request $request, string $school_id)
    {
        $student = Student::where('school_id', $school_id)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        try {
            DB::beginTransaction();

            $newVoucher = Voucher::where('is_given', 0)->lockForUpdate()->first();
            if (!$newVoucher) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'No available vouchers.'], 409);
            }

            // free old
            if (!is_null($student->voucher_id)) {
                Voucher::where('id', $student->voucher_id)->update(['is_given' => 0]);
            }

            // assign new
            $student->voucher_id = $newVoucher->id;
            $student->save();

            $newVoucher->is_given = 1;
            $newVoucher->save();

            DB::commit();

            return response()->json([
                'success'      => true,
                'voucher_code' => $newVoucher->voucher_code,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update one student's credentials (AJAX-friendly).
     */
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
            'birthday'               => ['nullable', 'date'],
        ]);

        $student = Student::where('school_id', $school_id)->first();
        if (!$student) {
            return $this->fail($request, 'Student not found for School ID: '.$school_id, 404);
        }

        $changed  = [];
        $snapshot = [];

        try {
            DB::beginTransaction();

            // Email
            $email_address  = $request->has('email_address')  ? (string)$request->input('email_address', '')  : null;
            $email_password = $request->has('email_password') ? (string)$request->input('email_password', '') : null;

            if (!is_null($email_address) || !is_null($email_password)) {
                $email = Email::firstOrNew(['sch_id_number' => $school_id]);
                $beforeAddr = (string)($email->email_address ?? '');
                $beforePwd  = (string)($email->password ?? '');
                if (!is_null($email_address))  $email->email_address = $email_address;
                if (!is_null($email_password)) $email->password      = $email_password;
                $email->save();
                if ($beforeAddr !== (string)$email->email_address || $beforePwd !== (string)$email->password) {
                    $changed[] = 'Email';
                }
                $snapshot['email_address']  = (string)$email->email_address;
                $snapshot['email_password'] = (string)$email->password;
            }

            // SATP
            if ($request->has('satp_password')) {
                $satp = Satpaccount::firstOrNew(['school_id' => $school_id]);
                $before = (string)($satp->satp_password ?? '');
                $satp->satp_password = (string)$request->input('satp_password', '');
                $satp->save();
                if ($before !== (string)$satp->satp_password) $changed[] = 'SATP';
                $snapshot['satp_password'] = (string)$satp->satp_password;
            }

            // Schoology
            if ($request->has('schoology_credentials')) {
                $sch = SchoologyCredential::firstOrNew(['school_id' => $school_id]);
                $before = (string)($sch->schoology_credentials ?? '');
                $sch->schoology_credentials = (string)$request->input('schoology_credentials', '');
                $sch->save();
                if ($before !== (string)$sch->schoology_credentials) $changed[] = 'Schoology';
                $snapshot['schoology_credentials'] = (string)$sch->schoology_credentials;
            }

            // Kumosoft
            if ($request->has('kumosoft_credentials')) {
                $ks = Kumosoft::firstOrNew(['school_id' => $school_id]);
                $before = (string)($ks->kumosoft_credentials ?? '');
                $ks->kumosoft_credentials = (string)$request->input('kumosoft_credentials', '');
                $ks->save();
                if ($before !== (string)$ks->kumosoft_credentials) $changed[] = 'Kumosoft';
                $snapshot['kumosoft_credentials'] = (string)$ks->kumosoft_credentials;
            }

            // Birthday
            if ($request->has('birthday')) {
                $before = (string)($student->birthday ?? '');
                $student->birthday = $request->input('birthday') ?: null;
                $student->save();
                if ($before !== (string)$student->birthday) $changed[] = 'Birthday';
                $snapshot['birthday'] = $student->birthday;
            }

            // Voucher
            $voucherCode = trim((string)$request->input('voucher_code', ''));
            $freeOld     = (bool)$request->boolean('free_old_voucher');

            if ($voucherCode !== '') {
                $newVoucher = Voucher::where('voucher_code', $voucherCode)->first();
                if (!$newVoucher) {
                    DB::rollBack();
                    return $this->fail($request, 'Voucher code not found.', 422);
                }
                if ((int)$newVoucher->is_given === 1 && (int)$student->voucher_id !== (int)$newVoucher->id) {
                    DB::rollBack();
                    return $this->fail($request, 'Voucher code is already assigned to someone else.', 422);
                }
                if (!is_null($student->voucher_id) && (int)$student->voucher_id !== (int)$newVoucher->id) {
                    Voucher::where('id', $student->voucher_id)->update(['is_given' => 0]);
                }
                $student->voucher_id = $newVoucher->id;
                $student->save();
                $newVoucher->is_given = 1;
                $newVoucher->save();
                $changed[] = 'Voucher';
                $snapshot['voucher_code'] = (string)$newVoucher->voucher_code;
            } elseif ($freeOld) {
                if (!is_null($student->voucher_id)) {
                    Voucher::where('id', $student->voucher_id)->update(['is_given' => 0]);
                    $student->voucher_id = null;
                    $student->save();
                    $changed[] = 'Voucher (unassigned)';
                    $snapshot['voucher_code'] = '';
                }
            } else {
                if (!array_key_exists('voucher_code', $snapshot)) {
                    if (!is_null($student->voucher_id)) {
                        $v = Voucher::find($student->voucher_id);
                        $snapshot['voucher_code'] = $v ? (string)$v->voucher_code : '';
                    } else {
                        $snapshot['voucher_code'] = '';
                    }
                }
            }

            DB::commit();

            $changedText = empty($changed) ? 'No fields changed' : implode(', ', $changed);

            if ($request->ajax()) {
                return response()->json([
                    'success'      => true,
                    'changed'      => $changed,
                    'changed_text' => $changedText,
                    'snapshot'     => array_merge(['school_id' => $school_id], $snapshot),
                ]);
            }

            return redirect()
                ->route('filters.index', ['q' => $school_id])
                ->with('status', 'Credentials updated successfully.')
                ->with('changed', $changed)
                ->with('changed_text', $changedText)
                ->with('auto_open', $school_id);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->fail($request, 'Error: '.$e->getMessage(), 500);
        }
    }

    private function fail(Request $request, string $message, int $code = 400)
    {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], $code);
        }
        return back()->withErrors(['server' => $message])->withInput();
    }
}
