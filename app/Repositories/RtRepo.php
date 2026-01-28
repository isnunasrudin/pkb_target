<?php

namespace App\Repositories;

use App\Models\CalonDewan;
use App\Models\Rt;

class RtRepo
{
    public static function getData($address, $partai = true)
    {

        $daftar_rt = $address->rt->sortBy('rt');
        $daftar_rt->loadCount('voters');
        $dapil = $address->dapil;

        $daftar_rt_yang_duplikat = $daftar_rt->groupBy('rt')->filter(function ($rt) {
            return $rt->count() > 1;
        })->keys()->toArray();

        // Daftar RT yang voters_count < 3
        $rt_dpt_invalid = $daftar_rt->filter(function (Rt $rt) {
            return $rt->voters_count <= 5;
        })->pluck('id')->toArray();

        $calon_dewans = CalonDewan::whereDapil($dapil)->when(!$partai, fn($q) => $q->whereNot('name', 'Partai'))->get();

        $data = collect($daftar_rt)->mapWithKeys(function (Rt $rt) use ($dapil, $calon_dewans) {
            $data = [];

            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $suara = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereSuaraId($rt->id)->first()?->total ?? 0;

                $data[$calon_dewan->id] = $suara;
            }

            return [$rt->id => $data];
        });

        $daftar_rt->loadCount('voters');
        $dpt = collect($daftar_rt)->mapWithKeys(function (Rt $rt) {
            return [$rt->id => $rt->voters_count];
        });

        $target = collect($daftar_rt)->mapWithKeys(function (Rt $rt) use ($dapil, $calon_dewans) {
            $data = [];

            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $target = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereSuaraId($rt->id)->first()?->target ?? 0;

                $data[$calon_dewan->id] = $target;
            }

            return [$rt->id => $data];
        });

        return compact('daftar_rt', 'calon_dewans', 'data', 'dpt', 'target', 'address', 'daftar_rt_yang_duplikat', 'rt_dpt_invalid');
    }
}
