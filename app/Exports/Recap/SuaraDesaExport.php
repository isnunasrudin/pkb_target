<?php

namespace App\Exports\Recap;

use App\Repositories\DesaRepo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SuaraDesaExport implements FromView, ShouldAutoSize
{
    public function __construct(public $kecamatan) {}

    public function view(): View
    {
        $data = DesaRepo::getData($this->kecamatan, false);

        return view('exports.suara', $data);
    }
}
