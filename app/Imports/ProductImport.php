<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductImport implements FromCollection
{
    public function collection() {
        return Product::get();
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string',
            '1' => 'required|string',
            '3' => 'required|string',
            '4' => 'required|string'
        ];
    }
}
