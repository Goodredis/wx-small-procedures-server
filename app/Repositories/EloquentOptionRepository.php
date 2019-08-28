<?php

namespace App\Repositories;

use App\Repositories\Contracts\OptionRepository;
use App\Models\Option;
use Illuminate\Database\Eloquent\Model;

class EloquentOptionRepository extends AbstractEloquentRepository implements OptionRepository
{

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], array $operatorCriteria = [])
    {
        $searchCriteria['orderby'] = (isset($searchCriteria['orderby']) && !empty($searchCriteria['orderby'])) ? $searchCriteria['orderby'] : 'created_at desc';
        return parent::findBy($searchCriteria, $operatorCriteria);
    }


    /**
     * @brief  批量删除
     * 物理删除
     * @param array $ids，注意id必须是数组，即使只有一个元素也得是数组格式
    */
    public function destroy($ids){
        foreach ($ids as $key => $id) {
            $option = $this -> findOne($id);
            $this -> delete($option);
        }
    }
}
