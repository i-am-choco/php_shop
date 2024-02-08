<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

        $data = [];

        foreach ($collection as $row)
        {
            $data[] = [
                'name' => $row[0],
                'description' => $row[1],
                'price' => $row[2],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Product::insert($data);
    }
}
