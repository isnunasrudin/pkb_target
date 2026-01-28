<?php

namespace App\Services;

use App\Models\CalonDewan;
use App\Models\Rt;
use Illuminate\Http\Request;

class HitungSebaran
{
    public static function hitungSebaran(Request $request)
    {
        $request->validate([
            'kecamatan' => 'required',
            'calon' => 'required',
            'target' => 'required',
        ]);

        // Ambil calon yang sedang dihitung
        $calon = CalonDewan::findOrFail($request->calon);

        // Ambil semua calon lain dalam dapil yang sama
        $calon_lain = CalonDewan::whereDapil($calon->dapil)
            ->where('id', '!=', $calon->id)
            ->get();

        // Ambil semua RT dalam kecamatan
        $rts = Rt::whereHas('address', fn($q) => $q->where('kecamatan', $request->kecamatan))
            ->withCount('voters')
            ->get();

        // Data final per RT
        $data = $rts->map(function (Rt $rt) use ($calon, $calon_lain) {

            // Existing suara calon saat ini
            $existing_calon_ini = $calon->suara()
                ->whereSuaraType(Rt::class)
                ->whereSuaraId($rt->id)
                ->first();

            $existing_suara_calon_ini = $existing_calon_ini->total ?? 0;
            $existing_target_calon_ini = $existing_calon_ini->target ?? 0;

            // Hitung target calon-calon lain di RT ini
            $target_calon_lain = 0;

            foreach ($calon_lain as $c) {
                $row = $c->suara()
                    ->whereSuaraType(Rt::class)
                    ->whereSuaraId($rt->id)
                    ->first();

                $target_calon_lain += max($row->target ?? 0, $row->total ?? 0);
            }

            // Kapasitas sisa sebenarnya
            // dd($rt->voters );
            $kapasitas_sisa = $rt->voters_count
                - ($target_calon_lain + $existing_suara_calon_ini);

            return (object)[
                'rt_id' => $rt->id,
                'voters' => $rt->voters_count,
                'existing_suara' => $existing_suara_calon_ini,
                'existing_target_calon_ini' => $existing_target_calon_ini,
                'target_calon_lain' => $target_calon_lain,
                'kapasitas_sisa' => max($kapasitas_sisa, 0),
            ];
        });

        // Hitung total existing suara
        $totalExisting = $data->sum('existing_suara');

        // Target minimal harus >= existing suara
        $target = max($request->target, $totalExisting);

        // Hitung kebutuhan tambahan
        $needToDistribute = $target - $totalExisting;

        // Total kapasitas semua RT
        $totalCapacity = $data->sum('kapasitas_sisa');

        // Jika kapasitas kurang, sesuaikan
        if ($totalCapacity < $needToDistribute) {
            $needToDistribute = $totalCapacity;
        }

        // Distribusi awal proporsional
        foreach ($data as $rt) {
            if ($totalCapacity == 0) {
                $rt->tambah = 0;
            } else {
                $rt->tambah = floor(
                    $needToDistribute * ($rt->kapasitas_sisa / $totalCapacity)
                );
            }
        }

        // Koreksi sisa pembulatan
        $assigned = $data->sum('tambah');
        $remaining = $needToDistribute - $assigned;

        while ($remaining > 0) {
            foreach ($data as $rt) {
                if ($remaining <= 0) break;

                if ($rt->tambah < $rt->kapasitas_sisa) {
                    $rt->tambah++;
                    $remaining--;
                }
            }
        }

        // Hitung target_final
        foreach ($data as $rt) {
            $rt->target_final = $rt->existing_suara + $rt->tambah;

            // Tidak boleh melebihi voters
            if ($rt->target_final > $rt->voters) {
                $rt->target_final = $rt->voters;
            }
        }

        // Simpan ke database
        foreach ($data as $rt) {
            $calon->suara()->updateOrCreate(
                [
                    'suara_type' => Rt::class,
                    'suara_id' => $rt->rt_id,
                ],
                [
                    'target' => $rt->target_final,
                ]
            );
        }

        return back()->with('success', 'Target berhasil disebarkan dan disimpan');
    }
}
