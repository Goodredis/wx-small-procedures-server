<?php

namespace App\Transformers;

use App\Models\Framework;
use League\Fractal\TransformerAbstract;

class FrameworkTransformer extends TransformerAbstract
{
    public function transform(Framework $framework)
    {
        $formattedFramework = [
            'id'             => $framework->id,
            'name'           => $framework->name,
            'code'           => $framework->code,
            'start_date'     => (string)$framework->start_date,
            'end_date'       => (string)$framework->end_date,
            'type'           => $framework->type,
            'tax_ratio'      => $framework->tax_ratio,
            'price'          => $framework->price,
            'price_with_tax' => $framework->price_with_tax,
            'supplier_code'  => $framework->supplier_code,
            'supplier'       => $framework->supplier,
            'details'        => $framework->frameworkdetails,
            'status'         => $framework->status,
            'created_at'     => (string)$framework->created_at,
            'updated_at'     => (string)$framework->updated_at,
            'del_flag'       => $framework->del_flag,
        ];

        return $formattedFramework;
    }
}