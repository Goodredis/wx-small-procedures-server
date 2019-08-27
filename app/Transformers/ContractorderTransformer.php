<?php

namespace App\Transformers;

use App\Models\Contractorder;
use League\Fractal\TransformerAbstract;

class ContractorderTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['framework', 'supplier', 'quota'];

    public function transform(Contractorder $contractorder) {
        $formattedContractorder = [
            'id'                    => $contractorder->id,
            'name'                  => $contractorder->name,
            'code'                  => $contractorder->code,
            'dept_id'               => $contractorder->dept_id,
            'signer'                => $contractorder->signer,
            'project_id'            => $contractorder->project_id,
            'start_date'            => strtotime($contractorder->start_date),
            'end_date'              => strtotime($contractorder->end_date),
            'tax_ratio'             => $contractorder->tax_ratio,
            'price'                 => $contractorder->price,
            'price_with_tax'        => $contractorder->price_with_tax,
            'used_price'            => $contractorder->used_price,
            'status'                => intval($contractorder->status),
            'created_at'            => $contractorder->created_at,
            'updated_at'            => $contractorder->updated_at,
            'del_flag'              => intval($contractorder->del_flag),
        ];
        return $formattedContractorder;
    }

    /**
     * 获取所属合同框架
     * @param model quota
     * @return \League\Fractal\Resource\Item
     */
    public function includeFramework(Contractorder $contractorder) {
        $frameworkTransformer = new FrameworkTransformer();
        $frameworkTransformer = $frameworkTransformer->setDefaultIncludes(['details']);
        return $this->item($contractorder->frameworkInfo, $frameworkTransformer);
    }

    /**
     * 获取供应商
     * @param model quota
     * @return \League\Fractal\Resource\Item
     */
    public function includeSupplier(Contractorder $contractorder) {
        $supplierTransformer = new SupplierTransformer();
        $supplierTransformer = $supplierTransformer->setDefaultIncludes([]);
        return $this->item($contractorder->supplierInfo, $supplierTransformer);
    }

    /**
     * 获取合同订单配额
     * @param model quota
     * @return \League\Fractal\Resource\Collection
     */
    public function includeQuota(Contractorder $contractorder) {
        $quotaTransformer = new ContractorderquotaTransformer();
        $quotaTransformer = $quotaTransformer->setDefaultIncludes([]);
        return $this->collection($contractorder->orderQuotas, $quotaTransformer);
    }
}