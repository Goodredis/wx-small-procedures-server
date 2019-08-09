<?php

namespace App\Transformers;

use App\Models\Frameworkdetails;
use League\Fractal\TransformerAbstract;

class FrameworkdetailsTransformer extends TransformerAbstract
{
    public function transform(Frameworkdetails $frameworkdetails)
    {
        $formattedFrameworkdetails = [
            'id'             => $frameworkdetails->id,
            'framework_id'   => $frameworkdetails->framework_id,
            'framework'      => $frameworkdetails->framework,
            'tax_ratio'      => $frameworkdetails->tax_ratio,
            'price'          => $frameworkdetails->price,
            'price_with_tax' => $frameworkdetails->price_with_tax,
            'type'           => $frameworkdetails->type,
            'level'          => $frameworkdetails->level,
            'created_at'     => (string)$frameworkdetails->created_at,
            'updated_at'     => (string)$frameworkdetails->updated_at
        ];

        return $formattedFrameworkdetails;
    }
}