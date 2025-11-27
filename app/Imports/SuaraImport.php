<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\CalonDewan;
use App\Models\Tps;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SuaraImport implements ToCollection
{
    public function __construct(public string $sheetName) {}
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $data = [];

        $current = 0;
        $is_valid = false;
        $tps = [];
        $desa = '';
        $calon = [];

        foreach ($collection->toArray() as $row) {
            if(!$is_valid) {
                if($row[0] == 'NO' && $row[1] == 'URAIAN') {
                    $is_valid = true;
                    ++$current;
                    $desa = preg_replace("/DESA?\\/KEL /", "", $row[2]);
                    $desa = trim($desa);
                    $data[$desa] = [];
                    continue;
                }
                else {
                    continue;
                }
            }

            if($row[1] == "DATA PEROLEHAN SUARA PARTAI POLITIK DAN SUARA CALON") {
                $tps = array_filter(array_slice($row, 2));
                $data[$desa]['tps'] = $tps;
                continue;
            }

            if($row[1] == "Partai") {
                $calon = ['Partai'];
                $panjang = count($data[$desa]['tps']);
                $data[$desa]['Partai'] = array_slice($row, 2, $panjang);
                continue;
            }

            if(str_contains($row[1], 'JUMLAH SUARA SAH PARTAI')) {
                $is_valid = false;
                continue;
            }

            $calon[] = $row[1];
            $panjang = count($data[$desa]['tps']);
            $data[$desa][$row[1]] = array_slice($row, 2, $panjang);
        }

        $final = collect($data)->reduce(function($before, $val, $desa) {
            $tps = $val['tps'];
            unset($val['tps']);

            $data = [];
            foreach ($val as $key => $value) {
                $data[$key] = array_combine($tps, $value);
            }

            $before[$desa] = $data;
            return $before;
        }, []);
        

        $kecamatanName = trim(str_replace("KECAMATAN ", "", $this->sheetName));

        foreach ($final as $desa => $value) {
            $alamat = Address::whereDesa($desa)->whereKecamatan($kecamatanName)->first();
            if(!$alamat){
                dd($desa, $kecamatanName);
            }

            foreach($value as $calon => $tps) {
                $dewan = CalonDewan::firstOrCreate([
                    'name' => $calon,
                    'dapil' => $GLOBALS['dapil']
                ]);

                foreach ($tps as $key => $value) {

                    if($value === null || $value === '') {
                        continue;
                    }

                    $no_tps = str_pad((int)preg_replace('/[^0-9]/', '', $key), 3, '0', STR_PAD_LEFT);
                    $tps = Tps::where('no_tps', $no_tps)
                    ->where('address_id', $alamat->id)
                    ->first();

                    if (!$tps) {
                        throw new \Exception("TPS {$no_tps} di {$desa}, {$kecamatanName} tidak ditemukan, ID Address: {$alamat->id}, Suara: {$value}");
                    }

                    $dewan->suara()->create([
                        'suara_type' => Tps::class,
                        'suara_id' => $tps->id,
                        'total' => (int)$value
                    ]);
                }
            }
        }

    }
}
