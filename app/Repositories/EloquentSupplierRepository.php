<?php

namespace App\Repositories;

use App\Utils\File;
use App\Utils\Excel;
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
    public function save(array $data)
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
    public function findBy(array $searchCriteria = [], array $operatorCriteria = [])
    {
        $searchCriteria['orderby'] = (isset($searchCriteria['orderby']) && !empty($searchCriteria['orderby'])) ? $searchCriteria['orderby'] : 'created_at desc';
        $searchCriteria['del_flag'] = 1;
        $operatorCriteria['del_flag'] = '!=';
        if (isset($searchCriteria['name'])) {
            $searchCriteria['name'] = '%' . $searchCriteria['name'] . '%';
            $operatorCriteria['name'] = 'like';
        }
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
     * @brief  批量删除，将厂商的合同框架的删除(del_flag置为1)，不删除框架详情信息
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
     * @brief  导入厂商信息
     * @param $file 上传的文件
     * 如果上传的文件名有append则是增量导入，否则是覆盖导入
     */
    public function importSupplierBasicInfo($file){
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
            $res = $this -> save($value);
            if(!$res instanceof Supplier){
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
     * @brief  通过厂商名称获取厂商基本信息
     * @param  string|array names 厂商名称
     * @return array
     */
    public function getSupplierInfoByNames($names) {
        if(!is_array($names)){
            $names = array($names);
        }
        $supplier = $this -> model
            -> select('name','code')
            -> whereIn('name', $names)
            -> get()
            -> toArray();
        return  empty($supplier) ? array() : ((count($names) == 1) ? $supplier[0] : $supplier);
    }

    /**
     * @brief 获取厂商的字典，只包含简单的信息id，name，code
     * @param string name 模糊查询厂商名
     * @return array
     */
    public function getSupplierDictionary($name = ''){
        $query_builder = $this -> model
            -> select('id', 'name', 'code', 'status')
            -> where('del_flag', '!=', 1)
            -> orderBy('id', 'desc');

        if(!empty($name)){
            $query_builder = $query_builder -> where ('name', 'like', '%' . $name . '%');
        }

        $suppliers = $query_builder -> get() -> toArray();

        return empty($suppliers) ? array() : $suppliers;
    }

}
