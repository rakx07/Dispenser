<?php

namespace App\Imports;

use App\Models\Email;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmailImport implements ToModel, WithHeadingRow
{
    public $skipped = 0; // Track the number of skipped records

    /**
     * Map each row of the Excel file to the Email model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if the record already exists in the database
        $existingEmail = Email::where('email_address', $row['email_address'])
            ->where('sch_id_number', $row['sch_id_number'])
            ->first();

        if ($existingEmail) {
            $this->skipped++; // Increment the skipped counter if the email exists
            return null;
        }

        // Return a new Email model instance for insertion
        return new Email([
            'first_name'    => $row['first_name'],
            'last_name'     => $row['last_name'],
            'email_address' => $row['email_address'],
            'password'      => $row['password'], // Store plain text password
            'sch_id_number' => $row['sch_id_number'],
        ]);
    }
}
