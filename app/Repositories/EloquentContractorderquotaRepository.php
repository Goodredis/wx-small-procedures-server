<?php

namespace App\Repositories;

use App\Models\Contractorder;
use App\Models\Contractorderquota;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ContractorderquotaRepository;

class EloquentContractorderquotaRepository extends AbstractEloquentRepository implements ContractorderquotaRepository
{

    //框架状态、类型对应字典
    private $string_map = array(
        'status' => [
            '执行中' => 1,
            '已完成' => 2
        ]
    );

    /*
     * @inheritdoc
     */
    public function save(array $data) {
        return parent::save($data);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data) {
        return parent::update($model, $data);
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $criteria) {
        return parent::findOneBy($criteria);
    }

    /**
     * @inheritdoc
     */
    public function destroy($ids){
        return parent::destroy($ids);
    }

}