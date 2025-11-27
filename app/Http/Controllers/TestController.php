<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SuaraImport;
use App\Imports\SuaraMultiImport;
use App\Models\Address;
use App\Models\CalonDewan;
use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TestController extends Controller
{
    public function __invoke(Request $request)
    {
        // return 'a';
        foreach (range(1, 6) as $dapil) {
            $GLOBALS['dapil'] = $dapil;
            $filePath = Storage::path('suara/Rekapitulasi DPRD KAB Dapil Trenggalek ' . $dapil . ' (fix).xlsx');
            $reader = IOFactory::createReaderForFile($filePath);
            
            // Opsional: Set ReadDataOnly agar lebih cepat (karena kita cuma butuh nama sheet)
            $reader->setReadDataOnly(true);

            // 2. Ambil semua nama sheet yang ada di file tersebut
            $allSheets = $reader->listWorksheetNames($filePath);

            // 3. Filter nama sheet yang berawalan "KECAMATAN"
            // Gunakan fungsi str_starts_with (PHP 8) atau strpos
            $filteredSheets = array_filter($allSheets, function($sheetName) {
                // Ubah ke uppercase jika ingin case-insensitive
                return str_starts_with(strtoupper($sheetName), 'KECAMATAN');
            });

            Excel::import(new SuaraMultiImport($filteredSheets), $filePath);
        }

    }

    public function sebar()
    {
        $calons = CalonDewan::with(['suara' => function($q) {
            $q->whereSuaraType(Tps::class);
        }, 'suara.suara.voters'])->limit(10)->get();

        $final_abiez = [];

        foreach ($calons as $calon) {

            $suara_per_rt_final = []; // Penampung hasil akhir akumulasi

            $all_tps = $calon->suara;

            foreach ($all_tps as $suara_tps) {
                $tps = $suara_tps->suara;
                $total_suara_tps = $suara_tps->total; // Contoh: 100 suara

                $dpt_tps = $tps->voters->count();
                
                // Safety check division by zero
                if ($dpt_tps == 0) continue;

                $rt_pada_tps = $tps->voters->pluck('rt_id')->unique()->toArray();
                
                // Array sementara untuk perhitungan distribusi
                $calculation_list = [];
                $current_total_allocated = 0;

                // 1. Hitung alokasi dasar (Floor)
                foreach ($rt_pada_tps as $rt) {
                    $dpt_rt = Voter::whereRtId($rt)->whereTpsId($tps->id)->count();

                    // Hitung porsi eksak (jangan di-round dulu!)
                    $raw_share = ($dpt_rt / $dpt_tps) * $total_suara_tps;
                    
                    // Ambil bulat ke bawah
                    $floored_share = floor($raw_share);
                    
                    // Ambil pecahannya untuk penentuan prioritas sisa
                    $fraction = $raw_share - $floored_share;

                    $calculation_list[] = [
                        'rt_id' => $rt,
                        'allocated' => (int) $floored_share,
                        'fraction' => $fraction
                    ];

                    $current_total_allocated += $floored_share;
                }

                // 2. Hitung Sisa Suara yang belum terbagi
                $remainder = $total_suara_tps - $current_total_allocated;

                // 3. Jika ada sisa, bagikan ke RT dengan pecahan desimal terbesar
                if ($remainder > 0) {
                    // Sort berdasarkan fraction terbesar ke terkecil
                    usort($calculation_list, function ($a, $b) {
                        return $b['fraction'] <=> $a['fraction'];
                    });

                    // Distribusikan sisa +1 ke urutan teratas sampai sisa habis
                    for ($i = 0; $i < $remainder; $i++) {
                        $calculation_list[$i]['allocated'] += 1;
                    }
                }

                // 4. Masukkan ke array hasil akumulasi (suara_per_rt)
                foreach ($calculation_list as $item) {
                    $rt_id = $item['rt_id'];
                    $suara = $item['allocated'];

                    // Akumulasi suara per RT (Global)
                    if (isset($suara_per_rt_final[$rt_id])) {
                        $suara_per_rt_final[$rt_id] += $suara;
                    } else {
                        $suara_per_rt_final[$rt_id] = $suara;
                    }
                }
            }

            $final_abiez[$calon->name] = [
                'data' => $suara_per_rt_final,
                'total' => array_sum($suara_per_rt_final)
            ];
        }

        
        dd($final_abiez);
    }

    public function test2() {

        $calon = CalonDewan::first();
        $target = 5000;
        $dapil = $calon->dapil;
        $all_kecamatan = config('kecamatan.group_dapil')[$dapil];
        $all_tps = Tps::whereHas('address', function ($query) use ($all_kecamatan) {
            $query->whereIn('kecamatan', $all_kecamatan);
        })->withCount('voters')->get();
        $suara_sekarang = $calon->suara()->whereSuaraType(Tps::class)->whereIn('suara_id', $all_tps->pluck('id'))->sum('total');

    }
}
