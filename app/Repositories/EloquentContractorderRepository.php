<?php

namespace App\Repositories;

use App\Tools\File;
use App\Tools\Excel;
use Ramsey\Uuid\Uuid;
use App\Models\Contractorder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ContractorderRepository;

class EloquentContractorderRepository extends AbstractEloquentRepository implements ContractorderRepository
{

	/**
     * 获取合同订单List
     */
    public function getContractOrderInfos(array $searchCriteria = []) {
        $operatorCriteria = array();
        // 检索部门 Automation
    	// 检索订单名称
    	if (isset($searchCriteria['name'])) {
            $searchCriteria['name'] = '%' . $searchCriteria['name'] . '%';
            $operatorCriteria['name'] = 'like';
        }
        // 检索订单编号
        if (isset($searchCriteria['code'])) {
            $searchCriteria['code'] = '%' . $searchCriteria['code'] . '%';
            $operatorCriteria['code'] = 'like';
        }
        // 检索供应商
        // 检索项目名称
        // 检索项目编号
        // 检索订单状态 Automation 
        $searchCriteria['del_flag'] = 0;
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

    /**
     * 获取单条合同订单
     */
    public function getContractOrderInfoById($id) {
        $criteria = array('id' => $id, 'del_flag' => 0);
        return parent::findOneBy($criteria);
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model){
        return parent::update($model, ['del_flag' => 1]);
    }

}