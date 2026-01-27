<?php

namespace App\Exports\Recap;

use App\Models\Voter;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class DptKecamatanExport implements FromView, WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    public function view(): View
    {
        $data = collect(config('kecamatan.all'))->map(function ($kecamatan, $key) {
            return [
                'No' => ++$key,
                'Kecamatan' => "KEC. $kecamatan",
                'DPT' => Voter::whereRelation('rt.address', 'kecamatan', $kecamatan)->count(),
            ];
        });

        $data = $data->push([
            'No' => '',
            'Kecamatan' => 'TOTAL',
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
            'Kecamatan',
            'DPT',
        ];
    }

    public function title(): string
    {
        return 'REKAP KECAMATAN';
    }

    /**
     * Atur warna tab sheet
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getTabColor()->setRGB('FF0000');
            },
        ];
    }
}
