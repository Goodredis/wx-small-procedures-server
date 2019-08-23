<?php

namespace App\Transformers;

use App\Models\Frameworkdetails;
use League\Fractal\TransformerAbstract;

class FrameworkdetailsTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'framework'
    ];
    public function transform(Frameworkdetails $frameworkdetails)
    {
        $formattedFrameworkdetails = [
            'id'             => $frameworkdetails->id,
            'framework_id'   => $frameworkdetails->framework_id,
            'tax_ratio'      => round($frameworkdetails->tax_ratio, 2),
            'price'          => round($frameworkdetails->price, 2),
            'price_with_tax' => round($frameworkdetails->price_with_tax, 2),
            'type'           => $frameworkdetails->type,
            'level'          => $frameworkdetails->level,
            'created_at'     => $frameworkdetails->created_at,
            'updated_at'     => $frameworkdetails->updated_at
        ];

        return $formattedFrameworkdetails;
    }

    /**
     * @brief 通过合同框架基本信息的transformer包含合同框架的基本信息
     * @param model frameworkdetails
     * @return \League\Fractal\Resource\Item
     */
    public function includeFramework(Frameworkdetails $frameworkdetails)
    {
        $framework = $frameworkdetails->framework;
        $frameworkTransformer = new FrameworkTransformer();
        $frameworkTransformer -> setDefaultIncludes(['supplier']);
        return $this->item($framework, $frameworkTransformer);
    }
}