<?php

namespace App\Http\Controllers;

use App\Models\CalonDewan;
use App\Models\Rt;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

class CalonDewanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Builder $builder)
    {
        $calonDewan = CalonDewan::withTrashed()->withSum([
            'suara' => fn($query) => $query->whereSuaraType(Rt::class),
        ], 'total')->withSum([
            'suara' => fn($query) => $query->whereSuaraType(Rt::class),
        ], 'target')->orderBy('dapil')->orderBy('order')->orderBy('id')->get();

        return view('calon_dewan', compact('calonDewan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'order' => 'required',
            'dapil' => 'required',
        ]);

        CalonDewan::create($data);

        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalonDewan $calonDewan)
    {
        $data = $request->validate([
            'name' => 'required',
            'order' => 'required',
            'dapil' => 'required',
        ]);

        $calonDewan->update($data);

        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalonDewan $calonDewan)
    {
        if(!$calonDewan->deleted_at) {
            $calonDewan->delete();
        }
        else {
            $calonDewan->restore();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
