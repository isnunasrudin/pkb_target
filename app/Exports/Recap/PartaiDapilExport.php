<?php

namespace App\Exports\Recap;

use App\Models\Rt;
use App\Models\Suara;
use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class PartaiDapilExport implements FromView, WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    public function view(): View
    {
        $data = collect(config('dapil'))->map(function ($dapil, $key) {
            return [
                'No' => $key,
                'Dapil' => "Dapil $key",
                'Keterangan' => implode(', ', $dapil),
                'DPT' => $dpt = Voter::whereHas('rt.address', function ($query) use ($dapil) {
                    $query->whereIn('kecamatan', $dapil);
                })->count(),
                'Suara' => $suara = Suara::whereHasMorph('suara', [Tps::class], function ($query) use ($dapil) {
                    $query->whereHas('address', function ($query) use ($dapil) {
                        $query->whereIn('kecamatan', $dapil);
                    });
                })->whereRelation('calonDewan', 'name', 'Partai')->sum('total'),
                'Persentase' => floor($suara / $dpt * 100) . "%",
            ];
        });

        $data = $data->push([
            'No' => '',
            'Dapil' => 'TOTAL',
            'Keterangan' => '',
            'DPT' => $data->sum('DPT'),
            'Suara' => $data->sum('Suara'),
            'Persentase' => floor($data->sum('Suara') / $data->sum('DPT') * 100) . "%",

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
            'Suara',
            'Persentase'
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
