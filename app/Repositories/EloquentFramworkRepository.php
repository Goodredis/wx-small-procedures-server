<?php

namespace App\Repositories;

use App\Repositories\Contracts\FramworkRepository;
use App\Models\Framwork;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class EloquentFramworkRepository extends AbstractEloquentRepository implements FramworkRepository
{

    /*
     * @inheritdoc
     */
    public function save(array $data)
    {

        $framwork = parent::save($data);
        return $framwork;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $updatedFramwork = parent::update($model, $data);

        return $updatedFramwork;
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