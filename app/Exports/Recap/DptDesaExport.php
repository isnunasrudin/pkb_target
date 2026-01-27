<?php

namespace App\Exports\Recap;

use App\Models\Address;
use App\Models\Voter;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class DptDesaExport implements FromView, WithHeadings, ShouldAutoSize, WithTitle
{
    public function __construct(public $kecamatan) {}

    public function view(): View
    {
        $data = Address::whereKecamatan($this->kecamatan)->get()->map(function (Address $desa, $key) {
            return [
                'No' => ++$key,
                'Desa' => "DESA $desa->desa",
                'DPT' => Voter::whereRelation('rt', 'address_id', $desa->id)->count(),
            ];
        });

        $data = $data->push([
            'No' => '',
            'Desa' => 'TOTAL',
            'DPT' => $data->sum('DPT'),
        ]);

        return view('exports.dpt', [
            'data' => $data,
            'headers' => $this->headings(),
        ]);
    }

    public function headings(): array
    {
        return [
            'No',
            'Desa',
            'DPT',
        ];
    }

    public function title(): string
    {
        return 'KEC. ' . $this->kecamatan;
    }
}
