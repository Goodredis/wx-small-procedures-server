<?php

namespace App\Repositories;

use Exception;
use App\Utils\File;
use App\Utils\Excel;
use App\Models\Contractorder;
use App\Models\Framework;
use App\Models\Dept;
use App\Models\Supplier;
use App\Models\Contractorderquota;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ContractorderRepository;

class EloquentContractorderRepository extends AbstractEloquentRepository implements ContractorderRepository
{

    protected $contractOrderQuotaRepository;

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
        '已用额度' => 'used_price',
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

    public function __construct(Model $model) {
        parent::__construct($model);
        $this->contractOrderQuotaRepository = new EloquentContractorderquotaRepository(new Contractorderquota());
    }

    /*
     * @inheritdoc
     */
    public function save(array $data) {
        // 检测已用额度是否超过订单总金额
        if (isset($data['used_price']) && (intval($data['used_price']) > intval($data['price']))) {
            throw new Exception(trans('errorCode.160003'), 160003);
        }
        // 检测订单负责人是否为订单执行部门下人员

        // 转换截止时间为时间戳格式
        $data['start_date'] = isset($data['start_date']) ? date('Ymd', $data['start_date']) : '';
        $data['end_date'] = isset($data['end_date']) ? date('Ymd', $data['end_date']) : '';
        // 获取供应商code
        $eloquentFrameworkRepository = new EloquentFrameworkRepository(new Framework());
        $framework_info = $eloquentFrameworkRepository->findOne($data['framework_id'])->toArray();
        $data['supplier_code'] = $framework_info['supplier_code'];
        // 插入合同订单表
        $order = parent::save($data);
        // 插入合同订单配额表
        $orderQuotaData = $this->arrangeOrderQuotaSysData($order->id, $data);
        $this->contractOrderQuotaRepository->save($orderQuotaData);
        return $order;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        // 检测已用额度是否超过订单总金额
        if (isset($data['used_price']) && (intval($data['used_price']) > intval($data['price']))) {
            throw new Exception(trans('errorCode.160003'), 160003);
        }
        // 检测订单负责人是否为订单执行部门下人员

        // 如果存在 则转换 否之则不动
        if (isset($data['start_date'])) {
            $data['start_date'] = date('Ymd', $data['start_date']);
        }
        if (isset($data['end_date'])) {
            $data['end_date'] = date('Ymd', $data['end_date']);
        }
        // 如果框架合同变更 则同时变更供应商code
        $order_info = $model->toArray();
        if ($data['framework_id'] != $order_info['framework_id']) {
            $eloquentFrameworkRepository = new EloquentFrameworkRepository(new Framework());
            $framework_info = $eloquentFrameworkRepository->findOne($data['framework_id'])->toArray();
            $data['supplier_code'] = $framework_info['supplier_code'];
        }

        // 更新合同订单表
        $order = parent::update($model, $data);
        // 更新合同订单配额表
        $criteria = array('contract_order_id' => $order->id, 'parent_project_id' => '');
        $order_quota = $this->contractOrderQuotaRepository->findOneBy($criteria);
        if (empty($order_quota)) {
            throw new Exception(trans('errorCode.160004'), 160004);
        }
        $orderQuotaData = $this->arrangeOrderQuotaSysData($order->id, $data);
        $this->contractOrderQuotaRepository->update($order_quota, $orderQuotaData);
        return $order;
    }

	/**
     * @brief  获取合同订单list
     * @param    dept_id          部门
     * @param    name             订单名称
     * @param    code             订单编号
     * @param    supplier_code    供应商
     * @param    project_id       项目名称
     * @param    project_id       项目编号
     * @param    status           订单状态
     * @return   collection
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
        $searchCriteria['del_flag'] = 1;
        $operatorCriteria['del_flag'] = '!=';
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

    /**
     * @brief  获取单条合同订单
     * @param    id               订单id
     * @return   collection
     */
    public function getContractOrderInfoById($id) {
        $criteria = array('id' => $id, 'del_flag' => 0);
        return parent::findOneBy($criteria);
    }

    /**
     * @brief  删除单条合同订单
     */
    public function delete(Model $model){
        // 删除合同订单信息--逻辑删除
        $order = parent::update($model, ['del_flag' => 1]);
        // 删除相关订单配额信息--物理删除
        $criteria = array('contract_order_id' => $model->id, 'columns' => 'id');
        $order_quota = $this->contractOrderQuotaRepository->findBy($criteria);
        if (empty($order_quota)) {
            throw new Exception(trans('errorCode.160004'), 160004);
        }
        $ids = $order_quota->transform(function ($value, $key) {
            return $value->id;
        });
        $this->contractOrderQuotaRepository->destroy($ids->toArray());
        return $order;
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
            return ['err_code' => 110005, 'error_data' => $error_data];
        }
        //删除文档
        unlink($file_path);
        return true;
    }

    /**
     * 合同订单配额同步数据
     */
    private function arrangeOrderQuotaSysData($contract_order_id, $data) {
        $orderQuotaData = array();
        if (empty($contract_order_id)){
            throw new Exception(trans('errorCode.160001'), 160001);
        }
        if (!isset($data['project_id']) || empty($data['project_id'])){
            throw new Exception(trans('errorCode.160002'), 160002);
        }
        $orderQuotaData['contract_order_id'] = trim($contract_order_id);
        $orderQuotaData['signer']     = trim($data['signer']);
        $orderQuotaData['project_id'] = trim($data['project_id']);
        $orderQuotaData['parent_project_id'] = isset($data['parent_project_id']) ? trim($data['parent_project_id']) : '';
        $orderQuotaData['tax_ratio']  = intval($data['tax_ratio']);
        $used_price = isset($data['used_price']) ? intval($data['used_price']) : 0;
        $price = intval($data['price']) - $used_price;
        $orderQuotaData['price'] = round($price, 2);
        $orderQuotaData['price_with_tax'] = round($price * (intval($data['tax_ratio'])/100+1), 2);
        return $orderQuotaData;
    }

}