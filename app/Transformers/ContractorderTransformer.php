<?php

namespace App\Transformers;

use App\Models\Contractorder;
use League\Fractal\TransformerAbstract;

class ContractorderTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['framework'];

    public function transform(Contractorder $contractorder) {
        $formattedContractorder = [
            'id'                    => $contractorder->id,
            'name'                  => $contractorder->name,
            'code'                  => $contractorder->code,
            'dept_id'               => $contractorder->dept_id,
            'signer'                => $contractorder->signer,
            'project_id'            => $contractorder->project_id,
            'parent_project_id'     => $contractorder->parent_project_id,
            'start_date'            => strtotime($contractorder->start_date),
            'end_date'              => strtotime($contractorder->end_date),
            'tax_ratio'             => $contractorder->tax_ratio,
            'price'                 => $contractorder->price,
            'price_with_tax'        => $contractorder->price_with_tax,
            'status'                => intval($contractorder->status),
            'created_at'            => strtotime($contractorder->created_at),
            'updated_at'            => strtotime($contractorder->updated_at),
            'del_flag'              => intval($contractorder->del_flag),
        ];

        return $formattedContractorder;
    }

    public function includeFramework(Contractorder $contractorder) {
        return $this->item($contractorder->frameworkInfo, new FrameworkTransformer());
    }
}