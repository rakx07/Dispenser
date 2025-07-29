<?php

// app/Imports/KumosoftImport.php

namespace App\Imports;

use App\Models\Kumosoft;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KumosoftImport implements ToModel, WithHeadingRow
{
    public int $skippedCount = 0;

    public function model(array $row)
    {
        if (!isset($row['school_id'], $row['kumosoft_credentials'])) {
            $this->skippedCount++;
            return null;
        }

        if (Kumosoft::where('school_id', $row['school_id'])->exists()) {
            $this->skippedCount++;
            return null;
        }

        return new Kumosoft([
            'school_id' => $row['school_id'],
            'kumosoft_credentials' => $row['kumosoft_credentials'],
        ]);
    }
}
