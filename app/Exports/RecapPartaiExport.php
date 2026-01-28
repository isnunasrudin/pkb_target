<?php

namespace App\Exports;

use App\Exports\Recap\PartaiDapilExport;
use App\Exports\Recap\PartaiDesaExport;
use App\Exports\Recap\PartaiKecamatanExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RecapPartaiExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $kec = collect(config('kecamatan.all'))->map(function ($kecamatan) {
            return new PartaiDesaExport($kecamatan);
        });

        return [
            new PartaiDapilExport(),
            new PartaiKecamatanExport(),
            ...$kec,
        ];
    }
}
