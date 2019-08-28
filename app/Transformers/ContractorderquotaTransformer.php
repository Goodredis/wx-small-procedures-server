<?php

namespace App\Transformers;

use App\Models\Contractorderquota;
use League\Fractal\TransformerAbstract;

class ContractorderquotaTransformer extends TransformerAbstract
{

    public function transform(Contractorderquota $contractorderquota) {
        $formattedContractorderquota = [
            'id'                    => $contractorderquota->id,
            'contract_order_id'     => $contractorderquota->contract_order_id,
            'signer'                => $contractorderquota->signer,
            'project_id'            => $contractorderquota->project_id,
            'parent_project_id'     => $contractorderquota->parent_project_id,
            'tax_ratio'             => intval($contractorderquota->tax_ratio),
            'price'                 => $contractorderquota->price,
            'price_with_tax'        => $contractorderquota->price_with_tax,
            'status'                => intval($contractorderquota->status),
            'created_at'            => $contractorderquota->created_at,
            'updated_at'            => $contractorderquota->updated_at,
        ];
        return $formattedContractorderquota;
    }

}