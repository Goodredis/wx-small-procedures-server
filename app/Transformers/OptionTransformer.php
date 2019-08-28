<?php

namespace App\Transformers;

use App\Models\Option;
use League\Fractal\TransformerAbstract;

class OptionTransformer extends TransformerAbstract
{
    public function transform(Option $option)
    {
        $formattedOption = [
            'id'          => $option->id,
            'key'         => $option->key,
            'value'       => $option->value,
            'description' => $option->description,
            'created_at'  => $option->created_at,
            'updated_at'  => $option->updated_at,
        ];

        return $formattedOption;
    }
}