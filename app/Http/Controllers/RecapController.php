<?php

namespace App\Http\Controllers;

use App\Exports\RecapDptExport;
use App\Exports\RecapPartaiExport;
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
}
