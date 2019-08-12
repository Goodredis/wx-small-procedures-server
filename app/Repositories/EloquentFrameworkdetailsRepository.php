<?php

namespace App\Repositories;

use App\Repositories\Contracts\FrameworkdetailsRepository;
use App\Models\Frameworkdetails;
use Illuminate\Database\Eloquent\Model;

class EloquentFrameworkdetailsRepository extends AbstractEloquentRepository implements FrameworkdetailsRepository
{

    /*
     * @inheritdoc
     * 新建合同框架的一条详情信息
     */
    public function save(array $data, $generateUidFlag = false)
    {
        return parent::save($data,$generateUidFlag);
    }

    /**
     * @inheritdoc
     * 更新合同框架的某条详情信息
     */
    public function update(Model $model, array $data)
    {
        $frameworkdetails = parent::update($model, $data);
        return $frameworkdetails;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [], $orderCriteria = 'framework_id,created_at desc')
    {
        return parent::findBy($searchCriteria, $operatorCriteria, $orderCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return parent::findOne($id);
    }
}