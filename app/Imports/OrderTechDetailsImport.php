<?php

namespace App\Imports;

use App\Models\OrderTechDetail;
use Maatwebsite\Excel\Concerns\ToModel;

class OrderTechDetailsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new OrderTechDetail([
            //
        ]);
    }
}
