<?php

namespace App\Http\Controllers;

use App\Exports\Recap\SuaraDesaExport;
use App\Models\Address;
use App\Models\CalonDewan;
use App\Models\Rt;
use App\Models\Tps;
use App\Repositories\DapilRepo;
use App\Repositories\DesaRepo;
use App\Repositories\RtRepo;
use App\Repositories\TpsRepo;
use App\Services\HitungSebaran;
use App\Services\ResetSebaran;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SuaraController extends Controller
{
    public function showDapil()
    {
        return view('data.dapil');
    }

    public function tps(Address $address)
    {
        return view('data.list_tps', TpsRepo::getData($address));
    }

    public function dapil($dapil)
    {
        return view('data.list_dapil', DapilRepo::getData($dapil));
    }

    public function desa($kecamatan)
    {
        return view('data.list_desa', DesaRepo::getData($kecamatan));
    }

    public function desaRecap($kecamatan)
    {
        return Excel::download(new SuaraDesaExport($kecamatan), 'suara-desa.xlsx');
    }


    public function rt(Address $address)
    {
        return view('data.list_rt', RtRepo::getData($address));
    }

    public function hitung_sebaran(Request $request)
    {
        return HitungSebaran::hitungSebaran($request);
    }

    public function reset_sebaran(Request $request)
    {
        return ResetSebaran::resetSebaran($request);
    }
}
