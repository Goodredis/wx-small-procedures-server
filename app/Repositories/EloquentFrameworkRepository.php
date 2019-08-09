<?php

namespace App\Repositories;

use App\Repositories\Contracts\FrameworkRepository;
use App\Models\Framework;
use Illuminate\Database\Eloquent\Model;
use App\Models\Frameworkdetails;

class EloquentFrameworkRepository extends AbstractEloquentRepository implements FrameworkRepository
{
    /*
     * 增加合同框架，可同时添加合同框架的详情
     */
    public function save(array $data, $generateUidFlag = false){
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
    public function update(Model $model, array $data){
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
     * 按条件查询信息列表
     * 默认有搜索del_flag位为0的条件，即没有删除的
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [], $orderCriteria = 'created_at desc'){
        $searchCriteria['del_flag'] = 0;
        return parent::findBy($searchCriteria, $operatorCriteria, $orderCriteria);
    }

    /**
     * @inheritdoc
     * 搜索del_flag位为0的，即没有删除的
     */
    public function findOne($id){
        $searchCriteria = [
            'id'       => $id,
            'del_flag' => 0
        ];
        return parent::findOneBy($searchCriteria);
    }

    /**
     * @inheritdoc
     * @param bool $del
     * 如果为true就是物理删除即真正的删除，
     * 如果为false则是逻辑删除即将del_flag置为1
     */
    public function delete(Model $model, $del=true){
        //删除合同框架详情
        $details_model = new Frameworkdetails();
        $details_model -> where('framework_id', $model->id)->delete();

        //删除合同框架基本信息
        $result = parent::delete($model, $del);
        return $result;
    }

    /**
     * 批量删除
     * @param array $id，注意id必须是数组，即使只有一个元素也得是数组格式
     * @param bool $del
     * 如果为true就是物理删除即真正的删除，
     * 如果为false则是逻辑删除即将del_flag置为1
    */
    public function destroy($id, $del = true){
        //删除合同框架详情
        $details_model = new Frameworkdetails();
        $details_model -> whereIn('framework_id', $id)->delete();

        //删除合同框架基本信息
        $result = parent::destroy($id, $del);
        return $result;
    }

}