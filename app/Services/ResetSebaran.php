<?php

namespace App\Services;

use App\Models\CalonDewan;
use App\Models\Rt;
use Illuminate\Http\Request;

class ResetSebaran
{
    public static function resetSebaran(Request $request)
    {
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
