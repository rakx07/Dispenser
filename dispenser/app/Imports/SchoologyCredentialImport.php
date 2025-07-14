<?php

namespace App\Imports;

use App\Models\SchoologyCredential;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchoologyCredentialImport implements ToModel, WithHeadingRow
{
    public $skippedCount = 0;

    /**
     * Map each row of the Excel to a SchoologyCredential record
     */
    public function model(array $row)
    {
        // Basic validation: skip if school_id or credentials missing
        if (empty($row['school_id']) || empty($row['schoology_credentials'])) {
            return null;
        }

        // Prevent duplicate school_id entries
        if (SchoologyCredential::where('school_id', $row['school_id'])->exists()) {
            $this->skippedCount++;
            return null;
        }

        return new SchoologyCredential([
            'school_id' => $row['school_id'],
            'schoology_credentials' => $row['schoology_credentials'],
        ]);
    }
}
