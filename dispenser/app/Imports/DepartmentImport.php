<?php

namespace App\Imports;

use App\Models\Department;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DepartmentImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
         //added collection for tocoll
         foreach ($rows as $row) 
         {
 
             $department= Department::where('code', $row['code'])->first();
             if($department){
 
                 $department->update([
                     'name' => $row['name'],
                     'college_id' => $row['college_id'],
                     'status' => $row['status'],
                     
                    
                 ]);
             }else
 
             Department::create([
                 'code' => $row['code'],
                 'name' => $row['name'],
                 'college_id' => $row['college_id'],
                 'status' => $row['status'],
             ]);
         }
    }
}
