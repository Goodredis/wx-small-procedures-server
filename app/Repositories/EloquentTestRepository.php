<?php

namespace App\Repositories;

use App\Repositories\Contracts\TestRepository;
use App\Models\Test;
use Illuminate\Database\Eloquent\Model;

class EloquentTestRepository extends AbstractEloquentRepository implements TestRepository
{
    /*
     * @inheritdoc
     */
    public function save(array $data, $generateUidFlag = true)
    {
        $test = parent::save($data, $generateUidFlag);
        return $test;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $updatedTest = parent::update($model, $data);
        return $updatedTest;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [])
    {
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
