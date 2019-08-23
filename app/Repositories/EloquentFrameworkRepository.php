<?php

namespace App\Repositories;

use App\Tools\File;
use App\Tools\Excel;
use App\Repositories\Contracts\FrameworkRepository;
use App\Models\Framework;
use Illuminate\Database\Eloquent\Model;

class EloquentFrameworkRepository extends AbstractEloquentRepository implements FrameworkRepository
{

    //导入的对应字典
    private $format_column = array(
        '名称'     => 'name',
        '编号'     => 'code',
        '生效日期' => 'start_date',
        '截止日期' => 'end_date',
        '框架类型' => 'type',
        '税率'     => 'tax_ratio',
        '供应商'   => 'supplier_code',
        '税后价款' => 'price',
        '含税价款' => 'price_with_tax',
        '框架状态' => 'status'
    );
    //框架状态、类型对应字典
    private $string_map = array(
        'type' => [
            '开发' => 1,
            '测试' => 2
        ],
        'status' => [
            '执行中' => 1,
            '已完成' => 2
        ]
    );
    /*
     * 增加合同框架，可同时添加合同框架的详情
     */
    public function save(array $data, $generateUidFlag = true){
        if(isset($data['start_date'])){
            $data['start_date'] = date("Ymd",$data['start_date']);
        }
        if(isset($data['end_date'])){
            $data['end_date'] = date("Ymd",$data['end_date']);
        }
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
    public function findBy(array $searchCriteria = [], $operatorCriteria = []){
        $searchCriteria['orderby'] = (isset($searchCriteria['orderby']) && !empty($searchCriteria['orderby'])) ? $searchCriteria['orderby'] : 'created_at desc';
        $searchCriteria['del_flag'] = 0;
        return parent::findBy($searchCriteria, $operatorCriteria);
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
            $this -> delete($framework);
        }
    }

    /**
     * 导入框架基本信息
     * @param $file 上传的文件
     * 如果上传的文件名有append则是增量导入，否则是覆盖导入
     */
    public function importFrameworkBasicInfo($file){
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
        $error_data = array();
        //循环插入数据表
        foreach ($data as $key => $value) {
            $value['type'] = isset($this -> string_map['type'][$value['type']]) ? $this -> string_map['type'][$value['type']] : $value['type'];
            $value['status'] = isset($this -> string_map['status'][$value['status']]) ? $this -> string_map['status'][$value['status']] : $value['status'];
            $res = $this -> save($value);
            if(!$res instanceof Framework){
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

    /**
     * @brief  通过框架名称获取框架基本信息
     * @param  string names 多个用逗号隔开
     * @return array
     */
    public function getFrameworkInfoByNames($names) {
        $framework = parent::findBy(array('name' => $names))->toArray();
        return !strpos($names, ",") ? array_pop($framework['data']) : $framework['data'];
    }
}