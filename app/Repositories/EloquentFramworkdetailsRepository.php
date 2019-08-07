<?php

namespace App\Repositories;

use App\Repositories\Contracts\FramworkdetailsRepository;
use App\Models\Framworkdetails;
use Illuminate\Database\Eloquent\Model;

class EloquentFramworkdetailsRepository extends AbstractEloquentRepository implements FramworkdetailsRepository
{

    /*
     * @inheritdoc
     */
    public function save(array $data)
    {

        $framworkdetails = parent::save($data);
        return $framworkdetails;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $updatedFramworkdetails = parent::update($model, $data);

        return $updatedFramworkdetails;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [])
    {
        return parent::findBy($searchCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return parent::findOne($id);
    }
}