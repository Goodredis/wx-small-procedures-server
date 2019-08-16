<?php

namespace App\Repositories;

use App\Repositories\Contracts\SupplierRepository;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;

class EloquentSupplierRepository extends AbstractEloquentRepository implements SupplierRepository
{

    /*
     * @inheritdoc
     */
    public function save(array $data, $generateUidFlag = false)
    {
        $supplier = parent::save($data);
        return $supplier;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        //如果更新了厂商的编号code，则更新所有合同框架表的supplier_code,因为合同框架表的supplier_code是外键
        if($data['code'] != $model->code){
            $model->framework()->update(['supplier_code'=>$data['code']]);
        }
        $updatedSupplier = parent::update($model, $data);

        return $updatedSupplier;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [])
    {
        $searchCriteria['orderby'] = (isset($searchCriteria['orderby']) && !empty($searchCriteria['orderby'])) ? $searchCriteria['orderby'] : 'created_at desc';
        $searchCriteria['del_flag'] = 0;
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return parent::findOne($id);
    }
}