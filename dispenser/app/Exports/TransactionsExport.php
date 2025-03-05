<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Transaction::with('student.course')->get();
    }

    public function headings(): array
    {
        return [
            'School ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Course',
            'Accessed At',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->student->school_id,
            $transaction->student->firstname,
            $transaction->student->middlename,
            $transaction->student->lastname,
            $transaction->student->course ? $transaction->student->course->name : 'N/A',
            $transaction->accessed_at,
        ];
    }
}
