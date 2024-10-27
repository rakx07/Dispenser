<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Satpaccount;

class SatpaccountImport implements ToCollection, WithHeadingRow
{
    public $skippedCount = 0; // Initialize a variable to count skipped entries

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check if a record with the same school_id already exists
            if (Satpaccount::where('school_id', $row['school_id'])->exists()) {
                $this->skippedCount++; // Increment the skipped count
                continue; // Skip this iteration if a duplicate is found
            }

            // Create a new Satpaccount entry if no duplicate is found
            Satpaccount::create([
                'school_id' => $row['school_id'],
                'satp_password' => $row['satp_password'],
            ]);
        }
    }
}