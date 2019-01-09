<?php

namespace App\Exports;

use App\Models\EMSuserUpload;
use Maatwebsite\Excel\Concerns\FromCollection;

class EMSExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return EMSuserUpload::all();
    }
}
