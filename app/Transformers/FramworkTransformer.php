<?php

namespace App\Transformers;

use App\Models\Framwork;
use League\Fractal\TransformerAbstract;

class FramworkTransformer extends TransformerAbstract
{
    public function transform(Framwork $framwork)
    {
        $formattedFramwork = [
            'id'         => $framwork->id,
            'name'       => $framwork->name,
            'code'       => $framwork->code,
            'start_date' => (string)$framwork->start_date,
            'end_date'   => (string)$framwork->end_date,
            'type'       => $framwork->type,
            'tax_ratio'  => $framwork->tax_ratio,
            'supplier'   => $framwork->supplier,
            'status'     => $framwork->status,
            'created_at' => $framwork->created_at,
            'updated_at' => $framwork->updated_at,
            'del_flag'   => $framwork->del_flag
        ];

        return $formattedFramwork;
    }
}