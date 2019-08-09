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
    public function save(array $data, $generateUidFlag = false)
    {
        $data['id'] = md5($data['code']);
        //先创建合同框架
        $framework = parent::save($data, $generateUidFlag);
        //如果有合同框架详情就创建合同框架详情
        if(isset($data['frameworkdetails'])){
            $frameworkdetails = $data['frameworkdetails'];
            $framework->frameworkdetails()->createMany($frameworkdetails);
        }
        return $framework;
    }

    /**
     * 修改合同框架,可同时修改合同框架的详情信息
     */
    public function update(Model $model, array $data)
    {
        //如果更新了合同框架的编号code，则更新所有详情表的framework_id,因为合同框架表的id是根据code经过md5生成的
        if($data['code'] != $model->code){
            $data['id'] = md5($data['code']);
            $framework = $this -> findOne($model->id);
            $framework->frameworkdetails()->rawUpdate(['framework_id'=>$data['id']]);
        }

        //更新合同框架的基本信息
        $updatedFramework = parent::update($model, $data);

        //更新详情表
        if(isset($data['frameworkdetails'])){
            foreach ($data['frameworkdetails'] as $key => $value) {
                $details_conditions = [
                    'id' => $value['id'],
                    'framework_id' => $data['id']
                ];
                $value['framework_id'] = $data['id'];
                $updatedFramework->frameworkdetails()-> updateOrCreate($details_conditions,$value);
            }
        }

        return $updatedFramework;
    }

    /**
     * 按条件查询信息列表，添加了排序
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [], $orderCriteria = 'created_at')
    {
        return parent::findBy($searchCriteria, $operatorCriteria, $orderCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return parent::findOne($id);
    }
}