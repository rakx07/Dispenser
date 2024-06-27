<?php

namespace App\Imports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CourseImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
       //added collection for tocoll
       foreach ($rows as $row) 
       {

           $course= Course::where('code', $row['code'])->first();
           if($course){

               $course->update([
                   'name' => $row['name'],
                   'college_id' => $row['department_id'],
                   'status' => $row['status'],
                   
                  
               ]);
           }else

           Course::create([
               'code' => $row['code'],
               'name' => $row['name'],
               'department_id' => $row['department_id'],
               'status' => $row['status'],
           ]);
       }
    }
}
