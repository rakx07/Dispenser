<?php

namespace App\Imports;

use App\Models\Voucher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VoucherImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Voucher::create([
                'voucher_code' => $row['voucher_code'],
                'is_given' => isset($row['is_given']) ? $row['is_given'] : 0,
            ]);
        }
    }
}
