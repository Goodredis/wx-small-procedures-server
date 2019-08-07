<?php

namespace App\Transformers;

use App\Models\Supplier;
use League\Fractal\TransformerAbstract;

class SupplierTransformer extends TransformerAbstract
{
    public function transform(Supplier $supplier)
    {
        $formattedSupplier = [
            'id'         => $supplier->id,
            'name'       => $supplier->name,
            'code'       => $supplier->code,
            'created_at' => $supplier->created_at,
            'updated_at' => $supplier->updated_at,
            'del_flag'   => $supplier->del_flag
        ];

        return $formattedSupplier;
    }
}