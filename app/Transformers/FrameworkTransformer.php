<?php

namespace App\Transformers;

use App\Models\Framework;
use League\Fractal\TransformerAbstract;

class FrameworkTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'supplier',
        'details'
    ];
    public function transform(Framework $framework)
    {
        $formattedFramework = [
            'id'             => $framework->id,
            'name'           => $framework->name,
            'code'           => $framework->code,
            'start_date'     => strtotime($framework->start_date),
            'end_date'       => strtotime($framework->end_date),
            'type'           => $framework->type,
            'tax_ratio'      => round($framework->tax_ratio, 2),
            'price'          => round($framework->price, 2),
            'price_with_tax' => round($framework->price_with_tax, 2),
            'supplier_code'  => $framework->supplier_code,
            'status'         => $framework->status,
            'created_at'     => $framework->created_at,
            'updated_at'     => $framework->updated_at,
            'del_flag'       => $framework->del_flag,
        ];

        return $formattedFramework;
    }

    /**
     * @brief 通过合同框架详情信息的transformer包含合同框架的详情信息
     * @param model framework
     * @return \League\Fractal\Resource\Collection
     */
    public function includeDetails(Framework $framework)
    {
        $frameworkdetails = $framework->frameworkdetails;
        $frameworkdetailsTransformer = new FrameworkdetailsTransformer();
        $frameworkdetailsTransformer -> setDefaultIncludes([]);
        return $this->collection($frameworkdetails,$frameworkdetailsTransformer);
    }

    /**
     * @brief 通过厂商基本信息的transformer包含厂商的基本信息
     * @param model framework
     * @return \League\Fractal\Resource\Item
     */
    public function includeSupplier(Framework $framework)
    {
        $supplier = $framework->supplier;
        $supplierTransformer = new SupplierTransformer();
        $supplierTransformer -> setDefaultIncludes([]);
        return $this->item($supplier,$supplierTransformer);
    }
}
