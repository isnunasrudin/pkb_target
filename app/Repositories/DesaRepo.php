<?php

namespace App\Repositories;

use App\Models\Address;
use App\Models\CalonDewan;
use App\Models\Rt;

class DesaRepo
{
    public static function getData($kecamatan, $partai = true)
    {
        $daftar_desa = Address::whereKecamatan($kecamatan)->with(['rt' => fn($q) => $q->withCount('voters')])->get();
        $dapil = config('kecamatan.dapil.' . $kecamatan);

        $calon_dewans = CalonDewan::whereDapil($dapil)->when(!$partai, fn($q) => $q->whereNot('name', 'Partai'))->get();

        $data = collect($daftar_desa)->mapWithKeys(function (Address $desa) use ($dapil, $calon_dewans) {
            $data = [];
            $all_rt = $desa->rt->pluck('id');

            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $suara = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereIn('suara_id', $all_rt)->sum('total');

                $data[$calon_dewan->id] = $suara;
            }

            return [$desa->id => $data];
        });

        $dpt = collect($daftar_desa)->mapWithKeys(function (Address $address) {
            return [$address->id => $address->rt->sum('voters_count')];
        });

        $target = collect($daftar_desa)->mapWithKeys(function (Address $address) use ($dapil, $calon_dewans) {
            $data = [];

            $all_rt = $address->rt->pluck('id');

            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $target = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereIn('suara_id', $all_rt)->sum('target');

                $data[$calon_dewan->id] = $target;
            }

            return [$address->id => $data];
        });

        return compact('daftar_desa', 'calon_dewans', 'data', 'dpt', 'target', 'kecamatan');
    }
}
