<?php

namespace App\Transformers;

use App\Models\Staff;
use League\Fractal\TransformerAbstract;

class StaffTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['company'];

    public function transform(Staff $staff) {
        $formattedStaff = [
            'id'                    => $staff->id,
            'name'                  => $staff->name,
            'gender'                => intval($staff->gender),
            'level'                 => intval($staff->level),
            'mobile'                => $staff->mobile,
            'email'                 => $staff->email,
            'birthday'              => strtotime($staff->birthday),
            'idcard'                => $staff->idcard,
            'ldap_id'               => $staff->ldap_id,
            'position'              => $staff->position,
            'type'                  => intval($staff->type),
            'label'                 => $staff->label,
            'status'                => intval($staff->status),
            'created_at'            => $staff->created_at,
            'updated_at'            => $staff->updated_at,
            'del_flag'              => intval($staff->del_flag),
            'highest_education'     => $staff->highest_education,
            'university'            => $staff->university,
            'major'                 => $staff->major
        ];
        return $formattedStaff;
    }

    public function includeCompany(Staff $staff) {
        $supplierTransformer = new SupplierTransformer();
        $supplierTransformer = $supplierTransformer->setDefaultIncludes([]);
        return $this->item($staff->companydetails, $supplierTransformer);
    }

}