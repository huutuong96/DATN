<?php

namespace App\Exports;

use App\Models\Image;
use Maatwebsite\Excel\Concerns\FromCollection;

class ImageExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Image::all();
    }
}
