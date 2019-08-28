<?php

namespace App\Transformers;

use App\Models\Authorization;
use League\Fractal\TransformerAbstract;

class AuthTransformer extends TransformerAbstract
{
    public function transform(Authorization $auth)
    {
        return $auth->toArray();
    }
}
