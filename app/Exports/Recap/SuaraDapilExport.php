<?php

namespace App\Exports\Recap;

use App\Repositories\DapilRepo;
use App\Repositories\DesaRepo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class SuaraDapilExport implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct(public $dapil) {}

    public function view(): View
    {
        $data = DapilRepo::getData($this->dapil, false);

        return view('exports.suaraAll', $data);
    }

    public function title(): string
    {
        return 'DAPIL ' . $this->dapil;
    }
}
