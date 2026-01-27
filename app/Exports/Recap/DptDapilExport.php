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

class DptDapilExport implements FromView, WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $data = collect(config('dapil'))->map(function ($dapil, $key) {
            return [
                'No' => $key,
                'Dapil' => "Dapil $key",
                'Keterangan' => implode(', ', $dapil),
                'DPT' => Voter::whereHas('rt.address', function ($query) use ($dapil) {
                    $query->whereIn('kecamatan', $dapil);
                })->count(),
            ];
        });

        $data = $data->push([
            'No' => '',
            'Dapil' => 'TOTAL',
            'Keterangan' => '',
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
            'Dapil',
            'Keterangan',
            'DPT',
        ];
    }

    public function title(): string
    {
        return 'REKAP DAPIL';
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
