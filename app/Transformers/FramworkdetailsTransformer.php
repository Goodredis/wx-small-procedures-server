<?php

namespace App\Transformers;

use App\Models\Framworkdetails;
use League\Fractal\TransformerAbstract;

class FramworkdetailsTransformer extends TransformerAbstract
{
    public function transform(Framworkdetails $framworkdetails)
    {
        $formattedFramworkdetails = [
            'id'             => $framworkdetails->id,
            'framework_id'   => $framworkdetails->framework_id,
            'tax_ratio'      => $framworkdetails->tax_ratio,
            'price'          => $framworkdetails->price,
            'price_with_tax' => $framworkdetails->price_with_tax,
            'type'           => $framworkdetails->type,
            'level'          => $framworkdetails->level,
            'created_at'     => $framworkdetails->created_at,
            'updated_at'     => $framworkdetails->updated_at
        ];

        return $formattedFramworkdetails;
    }
}