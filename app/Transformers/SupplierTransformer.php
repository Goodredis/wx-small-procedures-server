<?php

namespace App\Transformers;

use App\Models\Supplier;
use League\Fractal\TransformerAbstract;

class SupplierTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'framework'
    ];
    public function transform(Supplier $supplier)
    {
        $formattedSupplier = [
            'id'         => $supplier->id,
            'name'       => $supplier->name,
            'code'       => $supplier->code,
            'status'     => $supplier->status,
            'created_at' => $supplier->created_at,
            'updated_at' => $supplier->updated_at,
            'del_flag'   => $supplier->del_flag
        ];

        return $formattedSupplier;
    }

    /**
     * @brief 通过合同框架基本信息的transformer包含合同框架的基本信息
     * @param model supplier
     * @return \League\Fractal\Resource\Item
     */
    public function includeFramework(Supplier $supplier)
    {
        $framework = $supplier->framework;
        $frameworkTransformer = new FrameworkTransformer();
        $frameworkTransformer = $frameworkTransformer -> setDefaultIncludes(['details']);
        return $this->collection($framework, $frameworkTransformer);
    }
}