<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SuaraImport;
use App\Imports\SuaraMultiImport;
use App\Models\Address;
use App\Models\CalonDewan;
use App\Models\Rt;
use App\Models\Suara;
use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SuaraCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suara';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(Suara::count());

        $calons = CalonDewan::with(['suara' => function($q) {
            $q->whereSuaraType(Tps::class);
        }, 'suara.suara.voters'])->get();

        $final_abiez = [];

        foreach ($calons as $calon) {

            $suara_per_rt_final = []; // Penampung hasil akhir akumulasi

            $all_tps = $calon->suara;

            foreach ($all_tps as $suara_tps) {

                $bar->advance();

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

            foreach ($suara_per_rt_final as $rt_id => $total) {
                
                if (Suara::where('suara_id', $rt_id)->where('suara_type', Rt::class)->where('calon_dewan_id', $calon->id)->exists()) {
                    $this->warn("Suara untuk RT {$rt_id} dan calon {$calon->id} sudah ada, melewati...");
                    continue;
                }

                Suara::updateOrCreate([
                    'suara_id' => $rt_id,
                    'suara_type' => Rt::class,
                    'calon_dewan_id' => $calon->id,
                ], [
                    'total' => $total
                ]);

                // $this->info("Disimpan suara RT {$rt_id} untuk calon {$calon->id}: {$total}");
            }

            $final_abiez[$calon->name] = [
                'data' => $suara_per_rt_final,
                'total' => array_sum($suara_per_rt_final)
            ];
        }

        $bar->finish();
        $this->info("\nSebar suara selesai!");
    }
}
