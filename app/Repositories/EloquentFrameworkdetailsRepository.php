<?php

namespace App\Repositories;

use App\Repositories\Contracts\FrameworkdetailsRepository;
use App\Models\Frameworkdetails;
use Illuminate\Database\Eloquent\Model;

class EloquentFrameworkdetailsRepository extends AbstractEloquentRepository implements FrameworkdetailsRepository
{

    /*
     * @inheritdoc
     */
    public function save(array $data, $generateUidFlag = false)
    {

        $frameworkdetails = parent::save($data);
        return $frameworkdetails;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $updatedFrameworkdetails = parent::update($model, $data);

        return $updatedFrameworkdetails;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [], $orderCriteria = 'created_at')
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