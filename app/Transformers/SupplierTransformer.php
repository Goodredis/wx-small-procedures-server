<?php

namespace App\Transformers;

use App\Models\Supplier;
use League\Fractal\TransformerAbstract;

class SupplierTransformer extends TransformerAbstract
{
    public function transform(Supplier $supplier)
    {
        //整理开始日期与截止日期与框架详情
        $framework =array();
        foreach ($supplier->framework()->get() as $key => $value) {
            $details = $value->frameworkdetails()->get()->toArray();
            $value = $value -> toArray();
            $value['details'] = $details;
            $value['start_date'] = strtotime($value['start_date']);
            $value['end_date'] = strtotime($value['end_date']);
            $framework[$key] =$value;
        }
        $formattedSupplier = [
            'id'         => $supplier->id,
            'name'       => $supplier->name,
            'code'       => $supplier->code,
            'status'     => $supplier->status,
            'framework'  => $framework,
            'created_at' => $supplier->created_at,
            'updated_at' => $supplier->updated_at,
            'del_flag'   => $supplier->del_flag
        ];

        return $formattedSupplier;
    }
}