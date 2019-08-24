<?php

namespace App\Transformers;

use App\Models\Org;
use League\Fractal\TransformerAbstract;

class OrgTransformer extends TransformerAbstract
{
    public function transform(Org $org)
    {
        $formattedOrg = [
            'id'                    => $org->id,
            'code'                  => $org->code,
            'name'                  => $org->name,
            'order'                 => $org->order,
            'status'                => $org->status,
        ];

        return $formattedOrg;
    }
}
