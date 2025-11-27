<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SuaraMultiImport implements WithMultipleSheets
{
    public $sheets;
    public function __construct($sheets) {
        $this->sheets = $sheets;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->sheets as $sheet) {
            $sheets[] = new SuaraImport($sheet);
        }
        return $sheets;
    }
}
