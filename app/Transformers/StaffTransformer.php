<?php

namespace App\Transformers;

use App\Models\Staff;
use League\Fractal\TransformerAbstract;

class StaffTransformer extends TransformerAbstract
{
    public function transform(Staff $staff) {
        $formattedStaff = [
            'id'                    => $staff->id,
            'uid'                   => $staff->uid,
            'name'                  => $staff->name,
            'gender'                => $staff->gender,
            'level'                 => $staff->level,
            'mobile'                => $staff->mobile,
            'email'                 => $staff->email,
            'birthday'              => strtotime($staff->birthday),
            'idcard'                => $staff->idcard,
            'company'               => $staff->company,
            'position'              => $staff->position,
            'type'                  => $staff->type,
            'label'                 => $staff->label,
            'status'                => $staff->status,
            'created_at'            => strtotime($staff->created_at),
            'updated_at'            => strtotime($staff->updated_at),
            'del_flag'              => $staff->del_flag,
            'highest_education'     => $staff->highest_education,
            'university'            => $staff->university,
            'major'                 => $staff->major,
            'major_type'            => $staff->major_type,
            'major_level'           => $staff->major_level
        ];

        return $formattedStaff;
    }
}