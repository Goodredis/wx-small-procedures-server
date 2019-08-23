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