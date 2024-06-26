<?php

// namespace App\Imports;

// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToCollection;

// class CollegeImport implements ToCollection
// {
//     /**
//     * @param Collection $collection
//     */
//     public function collection(Collection $collection)
//     {
//         //
//     }
// }
namespace App\Imports;

use App\Models\College;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CollegeImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        //added collection for tocoll
        foreach ($rows as $row) 
        {

            $college= College::where('code', $row['code'])->first();
            if($college){

                $college->update([
                    'name' => $row['name'],
                    'status' => $row['status'],
                ]);
            }else

            College::create([
                'code' => $row['code'],
                'name' => $row['name'],
                'status' => $row['status'],
            ]);
        }


    }
}
