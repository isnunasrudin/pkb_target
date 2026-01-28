<?php

namespace App\Repositories;

use App\Models\CalonDewan;
use App\Models\Tps;

class TpsRepo
{
    public static function getData($address, $partai = true)
    {
        $daftar_tps = $address->tps;
        $dapil = $address->dapil;

        $calon_dewans = CalonDewan::whereDapil($dapil)->when(!$partai, fn($q) => $q->whereNot('name', 'Partai'))->get();

        $data = collect($daftar_tps)->mapWithKeys(function (Tps $tps) use ($dapil, $calon_dewans) {
            $data = [];

            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $suara = $calon_dewan->suara()->whereSuaraType(Tps::class)->whereSuaraId($tps->id)->first()->total;

                $data[$calon_dewan->id] = $suara;
            }

            return [$tps->id => $data];
        });

        return compact('daftar_tps', 'calon_dewans', 'data');
    }
}
