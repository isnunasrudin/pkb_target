<?php

namespace App\Exports;

use App\Exports\Recap\DptDapilExport;
use App\Exports\Recap\DptDesaExport;
use App\Exports\Recap\DptKecamatanExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RecapDptExport implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function sheets(): array
    {
        $kec = collect(config('kecamatan.all'))->map(function ($kecamatan) {
            return new DptDesaExport($kecamatan);
        });

        return [
            new DptDapilExport(),
            new DptKecamatanExport(),
            ...$kec,
        ];
    }
}
