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

        // Validate required fields
        if (empty($row['first_name']) || empty($row['last_name']) || empty($row['email_address'])) {
            $this->skipped++; // Increment skipped counter
            return null;
        }

        // Check for existing record
        $existingEmail = Email::where('email_address', $row['email_address'])->first();

        if ($existingEmail) {
            $this->skipped++; // Skip duplicate email addresses
            return null;
        }

        return new Email([
            'first_name'     => $row['first_name'],
            'last_name'      => $row['last_name'],
            'email_address'  => $row['email_address'],
            'password'       => $row['password'], // Assuming password is plaintext
            'sch_id_number'  => $row['sch_id_number'],
        ]);
    }
}

