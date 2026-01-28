<?php

namespace App\Repositories;

use App\Models\CalonDewan;
use App\Models\Rt;

class DapilRepo
{
    public static function getData($dapil, $partai = true)
    {
        $kecamatans = collect(config('kecamatan.dapil'))->filter(function ($value, $key) use ($dapil) {
            return $value == $dapil;
        });
        $calon_dewans = CalonDewan::whereDapil($dapil)->when(!$partai, fn($q) => $q->whereNot('name', 'Partai'))->get();

        $data = collect($kecamatans)->map(function ($dapil, $kecamatan) use ($calon_dewans) {
            $data = [];
            $all_rt = Rt::whereHas('address', function ($q) use ($kecamatan) {
                $q->where('kecamatan', $kecamatan);
            })->pluck('id');

            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $suara = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereIn('suara_id', $all_rt)->sum('total');

                $data[$calon_dewan->id] = $suara;
            }

            return $data;
        });

        $dpt = collect($kecamatans)->mapWithKeys(function ($dapil, $kecamatan) {

            $rts = Rt::whereHas('address', function ($q) use ($kecamatan) {
                $q->where('kecamatan', $kecamatan);
            })->withCount('voters')->get();

            return [$kecamatan => $rts->sum('voters_count')];
        });

        $target = collect($kecamatans)->mapWithKeys(function ($dapil, $kecamatan) use ($calon_dewans) {
            $data = [];

            $all_rt = Rt::whereHas('address', function ($q) use ($kecamatan) {
                $q->where('kecamatan', $kecamatan);
            })->pluck('id');

            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $target = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereIn('suara_id', $all_rt)->sum('target');

                $data[$calon_dewan->id] = $target;
            }

            return [$kecamatan => $data];
        });

        return compact('kecamatans', 'calon_dewans', 'data', 'target', 'dpt', 'dapil');
    }
}
