<?php

namespace App\Http\Controllers;

use App\Exports\ExportPusat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export()
    {
        $name = 'pusat_' . time() . '.xlsx';
        Excel::store(new ExportPusat, $name, 'public');

        return response()->json([
            'status' => 'success',
            'data' => [
                'url' => Storage::disk('public')->url($name)
            ]
        ]);
    }
}
