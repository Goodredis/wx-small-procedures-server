<?php

namespace App\Repositories;

use App\Repositories\Contracts\DeptRepository;
use App\Models\Dept;
use Illuminate\Database\Eloquent\Model;

class EloquentDeptRepository extends AbstractEloquentRepository implements DeptRepository
{
    /**
     * 按条件查询信息列表
     */
    public function findBy(array $searchCriteria = [], array $operatorCriteria = []){
        $searchCriteria['orderby'] = (isset($searchCriteria['orderby']) && !empty($searchCriteria['orderby'])) ? $searchCriteria['orderby'] : 'department_id';
        return parent::findBy($searchCriteria, $operatorCriteria);
    }
    /**
     * @brief  通过部所名称获取部所基本信息
     * @param  string|array names 部所名称
     * @return array
     */
    public function getDeptInfoByNames($names) {
        if(!is_array($names)){
            $names = array($names);
        }
        $dept = $this -> model
            -> whereIn('name', $names)
            -> get()
            -> toArray();
        return  empty($dept) ? array() : ((count($names) == 1) ? $dept[0] : $dept);
    }

    /**
     * 导入部所基本信息
     * @param $file 上传的文件
     * 如果上传的文件名有append则是增量导入，否则是覆盖导入
     */
    public function importDeptInfo($file){
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
            $res = $this -> save($value, false);
            if(!$res instanceof Dept){
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
     * @brief 获取部所的字典，只包含简单的信息id，name，department_id
     * @param string name 模糊查询部所名
     * @return array
     */
    public function getDeptDictionary($name = ''){
        $query_builder = $this -> model -> orderBy('department_id');

        if(!empty($name)){
            $query_builder = $query_builder -> where ('name', 'like', '%' . $name . '%');
        }

        $depts = $query_builder -> get() -> toArray();

        return empty($depts) ? array() : $depts;
    }
}