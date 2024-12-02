<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class MultiSheetImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'products' => new ProductImport(),
            'images' => new imagesProductImport(),
        ];
    }
}
