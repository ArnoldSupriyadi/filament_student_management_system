<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentExports implements FromCollection
{
    use Exportable;

    public function __construct(public Collection $records)
    {
            //    
    }

    public function collection()
    {
        return $this->records;
    }
}
