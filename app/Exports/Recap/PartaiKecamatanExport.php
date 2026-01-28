<?php

namespace App\Exports\Recap;

use App\Models\Rt;
use App\Models\Suara;
use App\Models\Voter;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class PartaiKecamatanExport implements FromView, WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    public function view(): View
    {
        $data = collect(config('kecamatan.all'))->map(function ($kecamatan, $key) {
            return [
                'No' => ++$key,
                'Kecamatan' => "KEC. $kecamatan",
                'DPT' => $dpt = Voter::whereRelation('rt.address', 'kecamatan', $kecamatan)->count(),
                'Suara' => $suara = Suara::whereHasMorph('suara', [Rt::class], function ($query) use ($kecamatan) {
                    $query->whereRelation('address', 'kecamatan', $kecamatan);
                })->whereRelation('calonDewan', 'name', 'Partai')->sum('total'),
                'Persentase' => floor($suara / $dpt * 100) . "%",
            ];
        });

        $data = $data->push([
            'No' => '',
            'Kecamatan' => 'TOTAL',
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
            'Kecamatan',
            'DPT',
            'Suara',
            'Persentase'
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
