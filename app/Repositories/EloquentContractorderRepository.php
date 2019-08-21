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
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = []) {
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
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

}