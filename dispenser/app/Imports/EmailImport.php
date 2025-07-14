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
        if (
            empty($row['first_name']) ||
            empty($row['last_name']) ||
            empty($row['email_address']) ||
            empty($row['sch_id_number'])
        ) {
            $this->skipped++; // Missing required field
            return null;
        }

        // Convert email to lowercase
        $row['email_address'] = strtolower($row['email_address']);

        // Skip if sch_id_number already exists
        if (Email::where('sch_id_number', $row['sch_id_number'])->exists()) {
            $this->skipped++; // Duplicate sch_id_number
            return null;
        }

        // Skip if email_address already exists
        if (Email::where('email_address', $row['email_address'])->exists()) {
            $this->skipped++; // Duplicate email_address
            return null;
        }

        // Default fallback password
        $password = !empty($row['password']) ? $row['password'] : 'defaultpassword';

        // Return the model to insert
        return new Email([
            'first_name'     => $row['first_name'],
            'last_name'      => $row['last_name'],
            'email_address'  => $row['email_address'],
            'password'       => $password, // Stored as plain text
            'sch_id_number'  => $row['sch_id_number'],
        ]);
    }
}
