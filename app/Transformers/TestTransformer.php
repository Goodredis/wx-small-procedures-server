<?php

namespace App\Transformers;

use App\Models\Test;
use League\Fractal\TransformerAbstract;

class TestTransformer extends TransformerAbstract
{
    public function transform(Test $test)
    {
        $formattedTest = [
            'id'               => $test->id,
            'name'             => (string) $test->name,
            'tag'              => (string) $test->tag
        ];

        return $formattedTest;
    }
}
