<?php

namespace App\Exports;

use App\QcData;
use Maatwebsite\Excel\Concerns\FromCollection;

class QcDataExport implements FromCollection
{
    public function collection()
    {
        return QcData::all();
    }
}
