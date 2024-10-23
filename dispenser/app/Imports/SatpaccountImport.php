<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Satpaccount;


class SatpaccountImport implements ToCollection , WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        //

        foreach ($rows as $row){
            Satpaccount::create ([
                'student_id' => $row['student_id'],
                'satp_password'=> $row['satp_password'],

            ]);

     
        }
    }
}
