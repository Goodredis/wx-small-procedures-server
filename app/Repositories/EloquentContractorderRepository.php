<?php

namespace App\Repositories;

use App\Tools\File;
use App\Tools\Excel;
use App\Models\Contractorder;
use App\Models\Framework;
use App\Models\Dept;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ContractorderRepository;

class EloquentContractorderRepository extends AbstractEloquentRepository implements ContractorderRepository
{

    //导入的对应字典
    private $format_column = array(
        '名称'     => 'name',
        '编号'     => 'code',
        '执行部门' => 'dept_id',
        '订单负责人' => 'signer',
        '项目名称' => 'project_id',
        '生效日期' => 'start_date',
        '截止日期' => 'end_date',
        '税率'     => 'tax_ratio',
        '税后价款' => 'price',
        '含税价款' => 'price_with_tax',
        '厂商'     => 'supplier_code',
        '框架编号' => 'framework_id',
        '订单状态' => 'status'
    );
    //框架状态、类型对应字典
    private $string_map = array(
        'status' => [
            '执行中' => 1,
            '已完成' => 2
        ]
    );
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

    /**
     * @brief  导入合同订单信息
     * @param $file 上传的文件
     * 如果上传的文件名有append则是增量导入，否则是覆盖导入
     */
    public function importContractOrderInfo($file){
        //上传文件，获取文件位置
        $file_path = File::upload($file);

        //获取导入的数组
        $data = Excel::import($file_path, $this -> format_column);

        //获取文件名，如果有append则是增量导入
        $patharr = explode('/',$file_path);
        $file_name = array_pop($patharr);
        //如果是覆盖导入就先清空表
        if (strpos($file_name, 'append') === false){
            $this -> model ->truncate();
        }

        $eloquentFrameworkeRepository = new EloquentFrameworkRepository(new Framework());
        $framework_ids = [];
        $eloquentDeptRepository = new EloquentDeptRepository(new Dept());
        $dept_ids = [];
        $eloquentSupplierRepository = new EloquentSupplierRepository(new Supplier());
        $supplier_codes = [];
        //记录添加失败的数据
        $error_data = array();
        //循环插入数据表
        foreach ($data as $key => $value) {
            $value['status'] = isset($this -> string_map['status'][$value['status']]) ? $this -> string_map['status'][$value['status']] : $value['status'];

            //获取部所的id
            if(!isset($dept_ids[$value['dept_id']])){
                $info = $eloquentDeptRepository -> getDeptInfoByNames($value['dept_id']);
                if(empty($info)){
                    $error_data['no_dept_id'][] = $value;
                    continue;
                }
                $dept_ids[$value['dept_id']] = $info['department_id'];
            }
            $value['dept_id'] = $dept_ids[$value['dept_id']];

            //获取框架的id
            if(!isset($framework_ids[$value['framework_id']])){

                $info = $eloquentFrameworkeRepository -> getFrameworkInfoByCodes($value['framework_id']);

                if(empty($info)){
                    $error_data['no_framework_id'][] = $value;
                    continue;
                }
                $framework_ids[$value['framework_id']] = $info['id'];
            }
            $value['framework_id'] = $framework_ids[$value['framework_id']];

            //获取厂商的code
            if(!isset($supplier_codes[$value['supplier_code']])){
                $info = $eloquentSupplierRepository -> getSupplierInfoByNames($value['supplier_code']);
                if(empty($info)){
                    $error_data['no_supplier_code'][] = $value;
                    continue;
                }
                $supplier_codes[$value['supplier_code']] = $info['code'];
            }
            $value['supplier_code'] = $supplier_codes[$value['supplier_code']];

            $res = $this -> save($value);
            if(!$res instanceof Contractorder){
                $error_data['create_failed'][] = $value;
            }
        }
        if(!empty($error_data)){
            return ['err_code' => 11005, 'error_data' => $error_data];
        }
        //删除文档
        unlink($file_path);
        return true;
    }

}