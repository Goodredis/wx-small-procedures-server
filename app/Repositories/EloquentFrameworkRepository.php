<?php

namespace App\Repositories;

use App\Repositories\Contracts\FrameworkRepository;
use App\Models\Framework;
use Illuminate\Database\Eloquent\Model;

class EloquentFrameworkRepository extends AbstractEloquentRepository implements FrameworkRepository
{
    /*
     * 增加合同框架，可同时添加合同框架的详情
     */
    public function save(array $data, $generateUidFlag = true){
        //先创建合同框架
        $framework = parent::save($data, $generateUidFlag);
        //如果有合同框架详情就创建合同框架详情
        if(isset($data['frameworkdetails'])){
            $frameworkdetails = $data['frameworkdetails'];
            //如果没有填详情的税率，就按框架基本信息的税率
            foreach ($frameworkdetails as $key => $value) {
                if(!isset($value['tax_ratio'])){
                    $frameworkdetails[$key]['tax_ratio'] = $data['tax_ratio'];
                }
            }
            $framework->frameworkdetails()->createMany($frameworkdetails);
        }
        return $framework;
    }

    /**
     * 修改合同框架,可同时修改合同框架的详情信息
     */
    public function update(Model $model, array $data){
        //更新详情表,先删除所有的详情，然后再添加
        $old_frameworkdetails = $model->frameworkdetails();
        foreach ($old_frameworkdetails->get() as $frameworkdetails) {
            $frameworkdetails->delete();
        }

        //更新合同框架的基本信息
        $updatedFramework = parent::update($model, $data);

        //添加合同框架的详情信息
        if(isset($data['frameworkdetails']) && !empty($data['frameworkdetails'])){
            $new_frameworkdetails = $data['frameworkdetails'];
            $updatedFramework->frameworkdetails()->createMany($new_frameworkdetails);
        }

        return $updatedFramework;
    }

    /**
     * 按条件查询信息列表
     * 默认有搜索del_flag位为0的条件，即没有删除的
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [], $orderCriteria = 'created_at desc'){
        $searchCriteria['del_flag'] = 0;
        return parent::findBy($searchCriteria, $operatorCriteria, $orderCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id){
        return parent::findOne($id);
    }

    /**
     * @inheritdoc
     * 删除某一个合同框架，不删除框架详情信息
     * 逻辑删除，将del_flag位置为1
     */
    public function delete(Model $model){
        //删除合同框架基本信息
        return parent::update($model, ['del_flag' => 1]);
    }

    /**
     * 批量删除，不删除框架详情信息
     * 逻辑删除，将del_flag位置为1
     * @param array $ids，注意id必须是数组，即使只有一个元素也得是数组格式
    */
    public function destroy($ids){
        foreach ($ids as $key => $id) {
            $framework = $this -> findOne($id);
            $this ->delete($framework);
        }
    }

}