<?php

namespace App\Exports\Recap;

use App\Models\Address;
use App\Models\Rt;
use App\Models\Suara;
use App\Models\Voter;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class PartaiDesaExport implements FromView, WithHeadings, ShouldAutoSize, WithTitle
{
    public function __construct(public $kecamatan) {}

    public function view(): View
    {
        $data = Address::whereKecamatan($this->kecamatan)->get()->map(function (Address $desa, $key) {
            return [
                'No' => ++$key,
                'Desa' => "DESA $desa->desa",
                'DPT' => $dpt = Voter::whereRelation('rt', 'address_id', $desa->id)->count(),
                'Suara' => $suara = Suara::whereHasMorph('suara', [Rt::class], function ($query) use ($desa) {
                    $query->whereRelation('address', 'address_id', $desa->id);
                })->whereRelation('calonDewan', 'name', 'Partai')->sum('total'),
                'Persentase' => floor($suara / $dpt * 100) . "%",
            ];
        });

        $data = $data->push([
            'No' => '',
            'Desa' => 'TOTAL',
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
            'Desa',
            'DPT',
            'Suara',
            'Persentase',
        ];
    }

    public function title(): string
    {
        return 'KEC. ' . $this->kecamatan;
    }
}
