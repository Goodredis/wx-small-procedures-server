<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class EloquentUserRepository extends AbstractEloquentRepository implements UserRepository
{
    /*
     * @inheritdoc
     */
    public function save(array $data)
    {
        $user = parent::save($data);

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $fillAbleProperties = $this->model->getFillable();

        foreach ($data as $key => $value) {

            // update only fillAble properties
            if (in_array($key, $fillAbleProperties)) {
                $model->$key = $value;
            }
        }

        // update the model
        $model->save();
        // get updated model from database
        $model = $this->findOne($model->openid);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], array $operatorCriteria = [])
    {
        return parent::findBy($searchCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return $this->findOneBy(['openid' => $id]);
    }
}
