<?php

namespace App\Exports;

use App\Models\CalonDewan;
use App\Models\Rt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles; // Import interface WithStyles
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // Import Worksheet untuk tipe hinting

class ExportPusat implements FromCollection, WithHeadings, WithStyles, WithStrictNullComparison
{
    public function headings(): array
    {
        return [
            'NO',
            'PROVINSI',
            'KABUPATEN',
            'KECAMATAN',
            'DESA',
            'DAPIL',
            'NAMA',
            'RT',
            'RW',
            'TARGET 2029',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data_final = collect();
        $increment = 1;

        CalonDewan::with(['suara' => fn($q) => $q->whereSuaraType(Rt::class)], 'suara.suara.address')->whereNot('name', 'Partai')->each(function ($calon) use ($data_final, &$increment) {
            $calon->suara->each(function ($suara) use (&$data_final, &$increment, $calon) {
                $data_final->push([
                    'no' => $increment++, // Increment dinaikkan di sini
                    'provinsi' => 'JAWA TIMUR',
                    'kabupaten' => 'TRENGGALEK',
                    'kecamatan' => $suara->suara->address->kecamatan,
                    'desa' => $suara->suara->address->desa,
                    'dapil' => $calon->dapil,
                    'name' => $calon->name,
                    'rt' => $suara->suara->rt,
                    'rw' => $suara->suara->rw,
                    'target' => $suara->target,
                ]);
            });
        });

        return $data_final;
    }
    
    /**
     * Metode untuk menerapkan styling, border, dan warna.
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // 1. Styling untuk Heading (Baris 1): Background Kuning dan Bold
        $headerStyle = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFF00'], // Kuning Terang
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];

        // Terapkan style heading ke baris 1
        $sheet->getStyle(1)->applyFromArray($headerStyle);
        
        
        // 2. Styling untuk Border pada Seluruh Data (termasuk Heading)
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $dataRange = 'A1:' . $highestColumn . $highestRow;

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'], // Warna border hitam
                ],
            ],
        ];

        // Terapkan border ke seluruh range data
        $sheet->getStyle($dataRange)->applyFromArray($borderStyle);
        
        // Opsional: Atur AutoSize untuk kolom agar konten pas
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}