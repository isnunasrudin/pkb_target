<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Rt;
use App\Models\Tps;
use App\Models\Voter;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class EksekusiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gas';

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
        $collectionTps = collect(Storage::files('dpt', true))->filter(function ($path) {
            return str_contains($path, '.csv');
        });

        $bar = $this->output->createProgressBar($collectionTps->count());
        

        $transformedData = $collectionTps->map(function ($path) {
            $parts = explode('/', $path);
            return [
                'kecamatan' => $parts[1],
                'desa'      => trim(preg_replace('/([0-9]|\.)/', '', $parts[2])),
                'path'      => $path,
            ];
        });

        $groupedData = $transformedData
            ->groupBy('kecamatan')
            ->map(function (Collection $kecamatanGroup) {
                return $kecamatanGroup
                    ->groupBy('desa')
                    ->map(function (Collection $desaGroup) {
                        return $desaGroup->pluck('path')->all();
                    })
                    ->all();
            })
            ->all();
            
        //GAS
        foreach ($groupedData as $kecamatan => $desaGroups) {
            foreach ($desaGroups as $desa => $tpss) {
                // DB::beginTransaction();
                foreach ($tpss as $tps) {
                    $lines = explode(PHP_EOL, Storage::get($tps));
                    $lines = collect($lines)->filter(function ($line) {
                        return preg_match('/(\d+),([^,]+),([LPA]),(\d+),([^,]+),(\d+),(\d+)/', $line);
                    });

                    $tps_name = value(function () use ($tps) {
                        preg_match('/TPS\s*(\d+)/', $tps, $matches);
                        return ($matches[1] ?? null);
                    });

                    $address = Address::firstOrCreate([
                        'kecamatan' => $kecamatan,
                        'desa' => $desa,
                    ]);

                    $tps = Tps::firstOrCreate([
                        'no_tps' => str_pad($tps_name, 3, '0', STR_PAD_LEFT),
                        'address_id' => $address->id
                    ]);

                    $bulk = [];
                    foreach ($lines as $line) {
                        [$no, $nama, $gender, $umur, $alamat, $rt, $rw] = explode(',', $line);

                        $alamat == $desa || throw new \Exception("Alamat tidak sesuai pada file {$tps}");

                        $rts = Rt::firstOrCreate([
                            'rt' => $rt,
                            'rw' => $rw,
                            'address_id' => $address->id
                        ]);

                        $bulk[] = [
                            'name' => $nama,
                            'gender' => $gender,
                            'age' => $umur,
                            'rt_id' => $rts->id,
                            'tps_id' => $tps->id
                        ];
                    }

                    Voter::insert($bulk);
                    $bar->advance();
                }
                // DB::commit();
            }
        }

        $bar->finish();
    }
}
