<?php

namespace App\Imports;

use App\Models\Email;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmailImport implements ToModel, WithHeadingRow
{
    public $skipped = 0; // Track skipped records

    public function model(array $row)
    {
        // Trim and clean up the data
        $row = array_map('trim', $row);

        // Validate required fields (Ensure they exist and are not empty)
        if (empty($row['first_name']) || empty($row['last_name']) || empty($row['email_address']) || empty($row['sch_id_number'])) {
            $this->skipped++; // Increment skipped counter
            return null; // Skip this row
        }

        // Convert email to lowercase for consistency
        $row['email_address'] = strtolower($row['email_address']);

        // Prevent duplicate email entries
        if (Email::where('email_address', $row['email_address'])->exists()) {
            $this->skipped++; // Count skipped duplicates
            return null;
        }

        // Ensure password is not empty (fallback to a default if missing)
        $password = !empty($row['password']) ? $row['password'] : 'defaultpassword';

        // Insert the new record
        return new Email([
            'first_name'     => $row['first_name'],  // **Ensure first_name is inserted**
            'last_name'      => $row['last_name'],
            'email_address'  => $row['email_address'],
            'password'       => $password, // Stored as plain text
            'sch_id_number'  => $row['sch_id_number'],
        ]);
    }
}
