<?php

namespace App\Repositories;

use Exception;
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
     * @brief 批量更新
     */
    public function updateBatch(array $data) {
        foreach ($data as $key => $value) {
            $quota = parent::findOne($value['id']);
            if (!$quota instanceof Contractorderquota) {
                throw new Exception(trans('errorCode160007'), 160007);
            }
            $this->update($quota, $value);
        }
    }

    /**
     * @brief 获取合同订单项目记录
     * @param Request $request
     * @param string  $id
     * @return collection
     */
    public function getProjectsFromOrder($id, array $criteria = []) {
        // 初始化where条件
        $searchCriteria['contract_order_id'] = $id;
        $searchCriteria['parent_project_id'] = '';
        $operatorCriteria['parent_project_id'] = '!=';
        // 初始化order条件
        $searchCriteria['orderby'] = isset($criteria['created_at']) ? trim($criteria['created_at']) : 'created_at';
        $quotas = parent::findBy($searchCriteria, $operatorCriteria)->toArray();
        return $quotas['data'];
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

    /**
     * @brief  合同订单配额分配(全量)
     * @param  string $id
     * @param  array  $criteria
     * @return
     */
    public function assignOrderToProjects($id, array $criteria) {
        set_time_limit(0);
        // Step.01  取出已分配数据
        $old_quotas = $this->getProjectsFromOrder($id);
        // Step.02  开始配额分配
        if (empty($old_quotas)) {
            // insert
            // 校验数据完整性
            $new_quotas = $this->filterQuotasData($criteria);
            if (isset($new_quotas['error'])) return $new_quotas;
            // 校验分配金额是否合理
            $this->checkAssignPrice($id, array_values(array_column($new_quotas, 'price')));
            // 批量插入
            Contractorderquota::insert($new_quotas);
        } else {
            // update            
            // 数据集中处理
            $quotas = $this->centralizeArrangeData($criteria, $old_quotas);
            while ($quotas) {
                switch (true) {
                    case array_key_exists('insert', $quotas):
                        $new_quotas = $this->filterQuotasData(array_pop($quotas));
                        Contractorderquota::insert($new_quotas);
                        break;
                    case array_key_exists('update', $quotas):
                        $new_quotas = $this->filterQuotasData(array_pop($quotas), false);
                        // @todo 如果该子项目已报工、则分配金额必须大于已报工总金额
                        $this->updateBatch($new_quotas);
                        break;
                    case array_key_exists('delete', $quotas):
                        // @todo 如果该子项目已报工则无法删除
                        $this->destroy(array_pop($quotas));
                        break;
                    default:
                        # code...
                        break;
                }
                if (isset($new_quotas['error'])) return $new_quotas;
            }
        }
        // Step.03  检测已分配配额是否超支
        // Step.04  return
        return $this->getProjectsFromOrder($id);
    }

    /**
     * @brief  过滤配额数据
     */
    private function filterQuotasData($data, $flag = true) {
        $time = time();
        $data = array_values($data);
        $columns = array('contract_order_id', 'signer', 'project_id', 'parent_project_id', 'tax_ratio', 'price', 'price_with_tax');
        for ($i=0; $i < count($data); $i++) { 
            $diffData = array_diff($columns, array_keys($data[$i]));
            if (!empty($diffData)) {
                $message = "Not found " . implode(',', array_values($diffData)) . " in the " . $data[$i]['project_id'];
                return array('err_code' => '', 'message' => $message);
            }
            for ($j=0; $j < count($columns); $j++) { 
                if (empty($data[$i][$columns[$j]])) {
                    $message = "Empty string of the parameter " . $columns[$j] . " in the " . $data[$i]['project_id'];
                    return array('err_code' => '', 'message' => $message);
                }
            }
            $checkQuotaData = $this->checkQuotaData($data[$i]);
            if (isset($checkQuotaData['error'])) return $checkQuotaData;
            // default value
            if ($flag) {
                $data[$i]['created_at'] = $time;
                $data[$i]['updated_at'] = $time;
            }
            $data[$i]['status'] = 1;
        }
        return $data;
    }

    /**
     * @brief  校验有效数据
     */
    private function checkQuotaData($item) {
        // 校验该条订单是否执行ing
        $model = new EloquentContractorderRepository(new Contractorder());
        $order_info = $model->getContractOrderInfoById($item['contract_order_id'])->toArray();
        if (empty($order_info) || $order_info['status'] != 1) {
            return array('err_code' => '', 'message' => '该订单不存在或未在可执行阶段');
        }
        // 校验订单负责人是否在职
        // 校验该条子项目是否已开题或者已结项
        // 校验该条父项目是否已开题或者已结项
    }

    /**
     * @brief  校验分配金额
     */
    private function checkAssignPrice($id, $prices) {
        $model = new EloquentContractorderRepository(new Contractorder());
        $order_info = $model->getContractOrderInfoById($id)->toArray();
        // 获取订单全部金额
        $all_price = intval($order_info['price']);
        // 获取订单已用金额
        $used_price = !empty($order_info['used_price']) ? intval($order_info['used_price']) : 0;
        // 获取父项目分配金额
        $parent_project = $this->findOneBy(array('contract_order_id' => $id, 'parent_project_id' => ''));
        $parent_project_price = intval($parent_project->price);
        // 如果子项目分配金额之和、大于可用额度 则分配超额
        if (array_sum($prices) > ($all_price-$used_price-$parent_project_price)) {
            throw new Exception(trans('errorCode.160005'), 160005);
        }
    }

    /**
     * @brief  数据集中处理
     * @param  array  new_data
     * @param  array  old_data
     * @param  array  quotas
     */
    private function centralizeArrangeData($new_data, $old_data) {
        $contract_order_id = $new_data[0]['contract_order_id'];
        $quotas = array('delete' => [], 'update' => [], 'insert' => []);
        if (empty($new_data) || empty($old_data)) {
            throw new Exception(trans('errorCode.160006'), 160006);
        }
        $new_ids = array_values(array_unique(array_column($new_data, 'id')));
        $old_ids = array_values(array_unique(array_column($old_data, 'id')));
        $quotas['delete'] = array_diff($old_ids, $new_ids);
        foreach ($new_data as $value) {
            if (!array_key_exists('id', $value)) {
                $quotas['insert'][] = $value;
            } else {
                $quotas['update'][] = $value;
            }
        }
        // 校验分配金额是否合理
        $this->checkAssignPrice($contract_order_id, array_merge(array_values(array_column($quotas['insert'], 'price')), array_values(array_column($quotas['update'], 'price'))));
        return $quotas;
    }

}