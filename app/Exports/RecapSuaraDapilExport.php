<?php

namespace App\Exports;

use App\Exports\Recap\SuaraDapilExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RecapSuaraDapilExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $dapils = collect(array_keys(config('dapil')))->map(function ($data) {
            return new SuaraDapilExport($data);
        });

        return $dapils->toArray();
    }
}
