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
    /**
     * GET /filters — search + list (paginated: 15/page)
     */
    public function index(Request $request)
    {
        $q             = trim((string) $request->query('q', ''));
        $course        = trim((string) $request->query('course', ''));
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
            // Course filter by code only (e.g., "BSIT")
            ->when($course !== '', function ($qq) use ($course) {
                $qq->whereHas('course', function ($c) use ($course) {
                    $c->where('code', $course);
                });
            })
            // Only students with any credentials (done at DB level so pagination stays correct)
            ->when($onlyWithCreds, function ($qq) {
                $qq->where(function ($w) {
                    $w->whereIn('school_id', Email::select('sch_id_number'))
                      ->orWhereIn('school_id', Satpaccount::select('school_id'))
                      ->orWhereIn('school_id', Kumosoft::select('school_id'))
                      ->orWhereIn('school_id', SchoologyCredential::select('school_id'))
                      ->orWhereNotNull('voucher_id');
                });
            })
            ->orderBy('lastname')
            ->orderBy('firstname');

        // Paginate 15 per page and keep query string
        $students = $studentsQuery->paginate(15)->appends($request->query());

        // Pull credential records for the CURRENT PAGE ONLY
        $schoolIds  = $students->pluck('school_id')->filter()->values();
        $voucherIds = $students->pluck('voucher_id')->filter()->values();

        $emails     = Email::whereIn('sch_id_number', $schoolIds)->get()->keyBy('sch_id_number');
        $satps      = Satpaccount::whereIn('school_id', $schoolIds)->get()->keyBy('school_id');
        $kumos      = Kumosoft::whereIn('school_id', $schoolIds)->get()->keyBy('school_id');
        $schoologys = SchoologyCredential::whereIn('school_id', $schoolIds)->get()->keyBy('school_id');
        $vouchers   = $voucherIds->isNotEmpty()
            ? Voucher::whereIn('id', $voucherIds)->get()->keyBy('id')
            : collect();

        // Auto-open modal when there is exactly one overall match and it is on this page
        $autoOpenId = null;
        if ($q !== '' && $students->total() === 1 && $students->count() === 1) {
            $autoOpenId = $students->first()->school_id;
        }

        // Course options (codes only used in UI datalist)
        $courses = Course::orderBy('name')->get(['id', 'code', 'name']);

        return view('filters.filter', [
            'q'           => $q,
            'course'      => $course,
            'onlyWith'    => $onlyWithCreds,
            'students'    => $students, // paginator
            'courses'     => $courses,
            'emails'      => $emails,
            'satps'       => $satps,
            'kumos'       => $kumos,
            'schoologys'  => $schoologys,
            'vouchers'    => $vouchers,
            'autoOpenId'  => $autoOpenId,
        ]);
    }

    /**
     * POST /filters/{school_id} — save changes
     * Creates missing credential rows and reports only actual changes.
     */
    public function update(Request $request, string $school_id)
    {
        $request->validate([
            'email_address'          => ['nullable', 'email'],
            'email_password'         => ['nullable', 'string'],
            'satp_password'          => ['nullable', 'string'],
            'schoology_credentials'  => ['nullable', 'string'],
            'kumosoft_credentials'   => ['nullable', 'string'],
            'birthday'               => ['nullable', 'date'],
            'voucher_code'           => ['nullable', 'string'],
            'free_old_voucher'       => ['nullable', 'boolean'],
        ]);

        $student = Student::where('school_id', $school_id)->first();
        if (!$student) {
            return back()->withErrors(['student' => 'Student not found for School ID: ' . $school_id]);
        }

        $changed = [];

        DB::beginTransaction();
        try {
            // EMAIL
            if ($request->filled('email_address') || $request->filled('email_password')) {
                $email = Email::firstOrNew(['sch_id_number' => $school_id]);
                $email->exists ? null : $email->fill(['email_address' => null, 'password' => null]);
                if ($request->filled('email_address'))  $email->email_address = $request->input('email_address');
                if ($request->filled('email_password')) $email->password      = $request->input('email_password');
                $email->save();
                if ($email->wasRecentlyCreated) $changed[] = 'Email (added)';
                elseif ($email->wasChanged())    $changed[] = 'Email (updated)';
            }

            // SATP
            if ($request->filled('satp_password')) {
                $satp = Satpaccount::firstOrNew(['school_id' => $school_id]);
                $satp->exists ? null : $satp->fill(['satp_password' => null]);
                $satp->satp_password = $request->input('satp_password');
                $satp->save();
                if ($satp->wasRecentlyCreated) $changed[] = 'SATP (added)';
                elseif ($satp->wasChanged())    $changed[] = 'SATP (updated)';
            }

            // SCHOOLOGY
            if ($request->filled('schoology_credentials')) {
                $sch = SchoologyCredential::firstOrNew(['school_id' => $school_id]);
                $sch->exists ? null : $sch->fill(['schoology_credentials' => null]);
                $sch->schoology_credentials = $request->input('schoology_credentials');
                $sch->save();
                if ($sch->wasRecentlyCreated) $changed[] = 'Schoology (added)';
                elseif ($sch->wasChanged())    $changed[] = 'Schoology (updated)';
            }

            // KUMOSOFT
            if ($request->filled('kumosoft_credentials')) {
                $ks = Kumosoft::firstOrNew(['school_id' => $school_id]);
                $ks->exists ? null : $ks->fill(['kumosoft_credentials' => null]);
                $ks->kumosoft_credentials = $request->input('kumosoft_credentials');
                $ks->save();
                if ($ks->wasRecentlyCreated) $changed[] = 'Kumosoft (added)';
                elseif ($ks->wasChanged())    $changed[] = 'Kumosoft (updated)';
            }

            // BIRTHDAY
            if ($request->filled('birthday')) {
                $old = $student->birthday;
                $student->birthday = $request->input('birthday');
                $student->save();
                if ($student->wasChanged('birthday')) {
                    $changed[] = $old ? 'Birthday (updated)' : 'Birthday (added)';
                }
            }

            // VOUCHER — only when different OR user checked "unassign"
            $voucherCode = trim((string) $request->input('voucher_code', ''));
            $freeOld     = (bool) $request->boolean('free_old_voucher');
            $currentVoucher = $student->voucher_id ? Voucher::find($student->voucher_id) : null;

            if ($voucherCode !== '') {
                $newVoucher = Voucher::where('voucher_code', $voucherCode)->first();
                if (!$newVoucher) {
                    DB::rollBack();
                    return back()->withErrors(['voucher_code' => 'Voucher code not found.'])->withInput();
                }

                // same as current: no change
                if ($currentVoucher && (int)$currentVoucher->id === (int)$newVoucher->id) {
                    // no-op
                } else {
                    if ((int)$newVoucher->is_given === 1) {
                        DB::rollBack();
                        return back()->withErrors(['voucher_code' => 'Voucher already assigned to another student.'])->withInput();
                    }
                    if ($currentVoucher) {
                        Voucher::where('id', $currentVoucher->id)->update(['is_given' => 0]);
                    }

                    $student->voucher_id = $newVoucher->id;
                    $student->save();

                    $newVoucher->is_given = 1;
                    $newVoucher->save();

                    $changed[] = 'Voucher (assigned)';
                }
            } elseif ($freeOld) {
                if ($currentVoucher) {
                    Voucher::where('id', $currentVoucher->id)->update(['is_given' => 0]);
                    $student->voucher_id = null;
                    $student->save();
                    $changed[] = 'Voucher (unassigned)';
                }
            }

            DB::commit();

            $changedText = empty($changed) ? 'No fields changed' : implode(', ', $changed);

            return redirect()
                ->route('filters.index', ['q' => $school_id])
                ->with('status', 'Changes saved successfully.')
                ->with('changed', $changed)
                ->with('changed_text', $changedText)
                ->with('auto_open', $school_id); // re-open same modal, show inline success
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['server' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * POST /filters/{school_id}/voucher/generate — assign next available voucher
     */
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
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
