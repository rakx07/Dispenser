<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;

class ReleaseVoucherController extends Controller
{
    /**
     * Show the release voucher page.
     */
    public function index()
    {
        // Vouchers generated in the previous step (flash data)
        $generatedVouchers = session('generated_vouchers', collect());

        return view('release_voucher.release', compact('generatedVouchers'));
    }

    /**
     * Generate N available vouchers (is_given = 0 or NULL).
     * No hard limit here; depends on user input.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1',
        ]);

        $qty = $request->input('quantity', 1);

        // Get earliest vouchers that are NOT yet given
        $generatedVouchers = Voucher::where(function ($q) {
                $q->whereNull('is_given')
                  ->orWhere('is_given', 0);
            })
            ->orderBy('id')
            ->limit($qty)
            ->get();

        if ($generatedVouchers->isEmpty()) {
            return redirect()
                ->route('release-voucher.index')
                ->with('status', 'No available vouchers to release. All vouchers are already given.');
        }

        // Flash vouchers to session so index() can display them
        session()->flash('generated_vouchers', $generatedVouchers);
        session()->flash('status', 'Generated '.$generatedVouchers->count().' voucher(s) ready for release.');

        return redirect()->route('release-voucher.index');
    }

    /**
     * Mark selected vouchers as GIVEN (is_given = 1).
     */
    public function release(Request $request)
    {
        $voucherIds = $request->input('voucher_ids', []);

        if (empty($voucherIds)) {
            return redirect()
                ->route('release-voucher.index')
                ->with('status', 'No vouchers selected for release.');
        }

        // Only update vouchers that are currently not yet given
        $updated = Voucher::whereIn('id', $voucherIds)
            ->where(function ($q) {
                $q->whereNull('is_given')
                  ->orWhere('is_given', 0);
            })
            ->update(['is_given' => 1]);

        return redirect()
            ->route('release-voucher.index')
            ->with('status', "Successfully released {$updated} voucher(s).");
    }

    /**
     * Show printable view for selected vouchers.
     */
    public function print(Request $request)
    {
        $voucherIds = $request->input('voucher_ids', []);

        if (empty($voucherIds)) {
            return redirect()
                ->route('release-voucher.index')
                ->with('status', 'No vouchers to print.');
        }

        $vouchers = Voucher::whereIn('id', $voucherIds)->orderBy('id')->get();

        return view('release_voucher.print', compact('vouchers'));
    }
}
