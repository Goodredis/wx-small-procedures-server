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
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], array $operatorCriteria = []) {
        return parent::findBy($searchCriteria, $operatorCriteria);
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
     * 合同订单配额分配(全量)
     * @param  string $id
     * @param  array  $criteria
     * @return
     */
    public function assignOrderToProjects($id, array $criteria) {
        // Step.01  校验数据完整性
        $new_quotas = $this->filterQuotasData($criteria);
        if (isset($new_quotas['error'])) return $new_quotas;
        // Step.02  取出已分配数据
        $old_quotas = $this->findBy(array('contract_order_id' => $id, 'parent_project_id' => ''), array('parent_project_id' => '!='))->toArray();
        // Step.03  开始配额分配
        if (empty($old_quotas['data'])) {
            // insert
            $this->checkAssignPrice($id, array_values(array_column($new_quotas, 'price')));
            Contractorderquota::insert($new_quotas);
        } else {
            // update
            echo 2;exit;
        }
        // Step.04  检测已分配配额是否超支
        // Step.05  return
        return $this->findBy(array('contract_order_id' => $id, 'parent_project_id' => ''), array('parent_project_id' => '!='))->toArray();
    }

    /**
     * 过滤配额数据
     */
    private function filterQuotasData($data) {
        $time = time();
        $data = array_values($data);
        $columns = array('contract_order_id', 'signer', 'project_id', 'parent_project_id', 'tax_ratio', 'price', 'price_with_tax');
        for ($i=0; $i < count($data); $i++) { 
            $diffData = array_diff_assoc($columns, array_keys($data[$i]));
            if (!empty($diffData)) {
                $message = "Not found " . implode(',', array_values($diffData)) . "in the" . $data[$i]['project_id'];
                return array('err_code' => '', 'message' => $message);
            }
            for ($j=0; $j < count($columns); $j++) { 
                if (empty($data[$i][$columns[$j]])) {
                    $message = "Empty string of the parameter " . $columns[$j] . "in the" . $data[$i]['project_id'];
                    return array('err_code' => '', 'message' => $message);
                }
            }
            $checkQuotaData = $this->checkQuotaData($data[$i]);
            if (isset($checkQuotaData['error'])) return $checkQuotaData;
            // fefault value
            $data[$i]['status'] = 1;
            $data[$i]['created_at'] = $time;
            $data[$i]['updated_at'] = $time;
        }
        return $data;
    }

    /**
     * 校验有效数据
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

}