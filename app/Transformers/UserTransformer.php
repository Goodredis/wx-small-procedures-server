<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        $formattedUser = [
            'id'                    => $user->id,
            'name'                  => $user->name,
            'gender'                => $user->gender,
            'mobile'                => $user->mobile,
            'email'                 => $user->email,
            'avatar'                => $user->avatar,
            'employee_number'       => $user->employee_number,
            'title'                 => $user->title,
            'order'                 => $user->order,
            'org'                   => $user->org,
            'status'                => $user->status,
        ];

        return $formattedUser;
    }
}
