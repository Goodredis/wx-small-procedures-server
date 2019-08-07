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
    public function save(array $data)
    {
        $supplier = parent::save($data);
        return $supplier;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $updatedSupplier = parent::update($model, $data);

        return $updatedSupplier;
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