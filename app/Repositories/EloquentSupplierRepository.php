<?php

namespace App\Repositories;

use App\Repositories\Contracts\SupplierRepository;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;

class EloquentSupplierRepository extends AbstractEloquentRepository implements SupplierRepository
{

    //导入的对应字典
    private $format_column = array(
        '名称'     => 'name',
        '编号'     => 'code'
    );

    /*
     * @inheritdoc
     */
    public function save(array $data, $generateUidFlag = false)
    {
        $supplier = parent::save($data);
        return $supplier;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        //如果更新了厂商的编号code，则更新所有合同框架表的supplier_code,因为合同框架表的supplier_code是外键
        if($data['code'] != $model->code){
            $model->framework()->update(['supplier_code'=>$data['code']]);
        }
        $updatedSupplier = parent::update($model, $data);

        return $updatedSupplier;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [])
    {
        $searchCriteria['orderby'] = (isset($searchCriteria['orderby']) && !empty($searchCriteria['orderby'])) ? $searchCriteria['orderby'] : 'created_at desc';
        $searchCriteria['del_flag'] = 0;
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return parent::findOne($id);
    }

    /**
     * @inheritdoc
     * 删除某一个厂商，，将厂商的合同框架的删除(del_flag置为1)，不删除框架详情信息
     * 逻辑删除，将del_flag位置为1
     */
    public function delete(Model $model){
        //将厂商的合同框架的删除
        $model->framework()->update(['del_flag'=>1]);
        //删除厂商信息
        return parent::update($model, ['del_flag' => 1]);
    }

    /**
     * 批量删除，将厂商的合同框架的删除(del_flag置为1)，不删除框架详情信息
     * 逻辑删除，将del_flag位置为1
     * @param array $ids，注意id必须是数组，即使只有一个元素也得是数组格式
    */
    public function destroy($ids){
        foreach ($ids as $key => $id) {
            $supplier = $this -> findOne($id);
            $this -> delete($supplier);
        }
    }

    /**
     * 导入框厂商信息
     * @param $file 上传的文件
     * 如果上传的文件名有append则是增量导入，否则是覆盖导入
     */
    public function importSupplierBasicInfo($file){
        //上传文件，获取文件位置
        $file_path = $this -> uploadFile($file);
        if(isset($file_path['err_code'])){
            return $file_path;
        }
        //获取导入的数组
        $data = $this -> import($file_path, $this -> format_column);
        if(isset($data['err_code'])){
            return $data;
        }

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
            $res = $this -> save($value);
            if(!$res instanceof Supplier){
                $error_data['create_failed'][] = $value;
            }
        }
        if(!empty($error_data)){
            return ['err_code' => 40005, 'error_data' => $error_data];
        }
        //删除文档
        unlink($file_path);
        return true;
    }
}