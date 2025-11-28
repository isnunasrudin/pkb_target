<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CalonDewan;
use App\Models\Rt;
use App\Models\Tps;
use Illuminate\Http\Request;

class SuaraController extends Controller
{

    public function showDapil()
    {
        return view('data.dapil');
    }

    public function tps(Address $address) {

        $daftar_tps = $address->tps;
        $dapil = $address->dapil;

        $calon_dewans = CalonDewan::whereDapil($dapil)->get();

        $data = collect($daftar_tps)->mapWithKeys(function(Tps $tps) use($dapil, $calon_dewans) {
            $data = [];
            
            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $suara = $calon_dewan->suara()->whereSuaraType(Tps::class)->whereSuaraId($tps->id)->first()->total;

                $data[$calon_dewan->id] = $suara;
            }
            
            return [$tps->id => $data];
        });

        return view('data.list_tps', compact('daftar_tps', 'calon_dewans', 'data'));
    }

    public function dapil($dapil) {
        $kecamatans = collect(config('kecamatan.dapil'))->filter(function($value, $key) use($dapil) {
            return $value == $dapil;
        });
        $calon_dewans = CalonDewan::whereDapil($dapil)->get();

        $data = collect($kecamatans)->map(function($dapil, $kecamatan) use($calon_dewans) {
            $data = [];
            $all_rt = Rt::whereHas('address', function($q) use($kecamatan) {
                $q->where('kecamatan', $kecamatan);
            })->pluck('id');
            
            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $suara = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereIn('suara_id', $all_rt)->sum('total');

                $data[$calon_dewan->id] = $suara;
            }
            
            return $data;
        });

        $dpt = collect($kecamatans)->mapWithKeys(function($dapil, $kecamatan) {
            
            $rts = Rt::whereHas('address', function($q) use($kecamatan) {
                $q->where('kecamatan', $kecamatan);
            })->withCount('voters')->get();

            return [$kecamatan => $rts->sum('voters_count')];
        });

        $target = collect($kecamatans)->mapWithKeys(function($dapil, $kecamatan) use($calon_dewans) {
            $data = [];

            $all_rt = Rt::whereHas('address', function($q) use($kecamatan) {
                $q->where('kecamatan', $kecamatan);
            })->pluck('id');
            
            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $target = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereIn('suara_id', $all_rt)->sum('target');

                $data[$calon_dewan->id] = $target;
            }
            
            return [$kecamatan => $data];
        });

        return view('data.list_dapil', compact('kecamatans', 'calon_dewans', 'data', 'target', 'dpt', 'dapil'));
    }

    public function desa($kecamatan) {
        $daftar_desa = Address::whereKecamatan($kecamatan)->with(['rt' => fn($q) => $q->withCount('voters')])->get();
        $dapil = config('kecamatan.dapil.' . $kecamatan);

        $calon_dewans = CalonDewan::whereDapil($dapil)->get();

        $data = collect($daftar_desa)->mapWithKeys(function(Address $desa) use($dapil, $calon_dewans) {
            $data = [];
            $all_rt = $desa->rt->pluck('id');
            
            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $suara = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereIn('suara_id', $all_rt)->sum('total');

                $data[$calon_dewan->id] = $suara;
            }
            
            return [$desa->id => $data];
        });

        $dpt = collect($daftar_desa)->mapWithKeys(function(Address $address) {
            return [$address->id => $address->rt->sum('voters_count')];
        });

        $target = collect($daftar_desa)->mapWithKeys(function(Address $address) use($dapil, $calon_dewans) {
            $data = [];

            $all_rt = $address->rt->pluck('id');
            
            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $target = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereIn('suara_id', $all_rt)->sum('target');

                $data[$calon_dewan->id] = $target;
            }
            
            return [$address->id => $data];
        });

        return view('data.list_desa', compact('daftar_desa', 'calon_dewans', 'data', 'dpt', 'target', 'kecamatan'));
    }

    public function rt(Address $address) {

        $daftar_rt = $address->rt->sortBy('rt');
        $daftar_rt->loadCount('voters');
        $dapil = $address->dapil;

        $daftar_rt_yang_duplikat = $daftar_rt->groupBy('rt')->filter(function($rt) {
            return $rt->count() > 1;
        })->keys()->toArray();

        // Daftar RT yang voters_count < 3
        $rt_dpt_invalid = $daftar_rt->filter(function(Rt $rt) {
            return $rt->voters_count <= 5;
        })->pluck('id')->toArray();

        $calon_dewans = CalonDewan::whereDapil($dapil)->get();

        $data = collect($daftar_rt)->mapWithKeys(function(Rt $rt) use($dapil, $calon_dewans) {
            $data = [];
            
            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $suara = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereSuaraId($rt->id)->first()?->total ?? 0;

                $data[$calon_dewan->id] = $suara;
            }
            
            return [$rt->id => $data];
        });

        $daftar_rt->loadCount('voters');
        $dpt = collect($daftar_rt)->mapWithKeys(function(Rt $rt) {
            return [$rt->id => $rt->voters_count];
        });

        $target = collect($daftar_rt)->mapWithKeys(function(Rt $rt) use($dapil, $calon_dewans) {
            $data = [];
            
            foreach ($calon_dewans->filter(fn($d) => $d->dapil === $dapil) as $calon_dewan) {
                $target = $calon_dewan->suara()->whereSuaraType(Rt::class)->whereSuaraId($rt->id)->first()?->target ?? 0;

                $data[$calon_dewan->id] = $target;
            }
            
            return [$rt->id => $data];
        });

        return view('data.list_rt', compact('daftar_rt', 'calon_dewans', 'data', 'dpt', 'target', 'address', 'daftar_rt_yang_duplikat', 'rt_dpt_invalid'));
    }

    public function hitung_sebaran(Request $request)
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
        $data = $rts->map(function(Rt $rt) use ($calon, $calon_lain) {

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

    public function reset_sebaran(Request $request) {
        $request->validate([
            'kecamatan' => 'required',
            'calon' => 'required',
        ]);

        $calon_dewan = CalonDewan::findOrFail($request->calon);

        $rts = Rt::whereHas('address', fn($q) => $q->where('kecamatan', $request->kecamatan))
            ->withCount('voters')
            ->get();

        foreach ($rts as $rt) {
            $calon_dewan->suara()->updateOrCreate(
                [
                    'suara_type' => Rt::class,
                    'suara_id' => $rt->id,
                ],
                [
                    'target' => 0,
                ]
            );
        }

        return redirect()->back()->with('success', 'Target berhasil direset');
    }

}
