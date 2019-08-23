<?php

namespace App\Transformers;

use App\Models\Dept;
use League\Fractal\TransformerAbstract;

class DeptTransformer extends TransformerAbstract
{

    public function transform(Dept $dept)
    {
        $formattedDept = [
            'id'             => $dept->id,
            'name'           => $dept->name,
            'department_id'  => $dept->department_id,
        ];

        return $formattedDept;
    }
}