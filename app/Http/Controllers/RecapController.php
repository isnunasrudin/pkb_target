<?php

namespace App\Http\Controllers;

use App\Exports\Recap\SuaraKecamatanExport;
use App\Exports\RecapDptExport;
use App\Exports\RecapPartaiExport;
use App\Exports\RecapSuaraDapilExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RecapController extends Controller
{
    public function index()
    {
        return view('recap');
    }

    public function dpt()
    {
        return Excel::download(new RecapDptExport(), 'recap-dpt.xlsx');
    }

    public function partai()
    {
        return Excel::download(new RecapPartaiExport(), 'recap-partai.xlsx');
    }

    public function kecamatan(Request $request)
    {
        return (new SuaraController)->desaRecap($request->kecamatan);
    }

    public function allKecamatan()
    {
        return Excel::download(new RecapSuaraDapilExport(), 'recap-kecamatan.xlsx');
    }
}
