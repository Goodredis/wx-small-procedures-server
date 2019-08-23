<?php

namespace App\Repositories;

use App\Tools\File;
use App\Tools\Excel;
use App\Repositories\Contracts\FrameworkdetailsRepository;
use App\Models\Frameworkdetails;
use App\Models\Framework;
use Illuminate\Database\Eloquent\Model;

class EloquentFrameworkdetailsRepository extends AbstractEloquentRepository implements FrameworkdetailsRepository
{

    //导入的对应字典
    private $format_column = array(
        '合同框架编号' => 'framework_id',
        '税率'         => 'tax_ratio',
        '税后单价'     => 'price',
        '税前单价'     => 'price_with_tax',
        '类型'         => 'type',
        '职级'         => 'level'
    );
    //框架详情类型、职级对应字典
    private $string_map = array(
        'type' => [
            '开发' => 1,
            '测试' => 2
        ],
        'level' => [
            '初级' => 1,
            '中级' => 2,
            '高级' => 3
        ]
    );

    /*
     * @inheritdoc
     * 新建合同框架的一条详情信息
     */
    public function save(array $data, $generateUidFlag = false)
    {
        return parent::save($data,$generateUidFlag);
    }

    /**
     * @inheritdoc
     * 更新合同框架的某条详情信息
     */
    public function update(Model $model, array $data)
    {
        $frameworkdetails = parent::update($model, $data);
        return $frameworkdetails;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [])
    {
        $searchCriteria['orderby'] = (isset($searchCriteria['orderby']) && !empty($searchCriteria['orderby'])) ? $searchCriteria['orderby'] : 'framework_id,created_at desc';
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
     * 导入框架基本信息
     * @param $file 上传的文件
     * 如果上传的文件名有append则是增量导入，否则是覆盖导入
     */
    public function importFrameworkDetailInfo($file){
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
        //记录所有已查出的框架id，以编号做为key
        $eloquentFrameworkeRepository = new EloquentFrameworkRepository(new Framework());
        $framework_ids = [];
        $error_data = array();
        //循环插入数据表
        foreach ($data as $key => $value) {
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

            $value['type'] = isset($this -> string_map['type'][$value['type']]) ? $this -> string_map['type'][$value['type']] : $value['type'];
            $value['level'] = isset($this -> string_map['level'][$value['level']]) ? $this -> string_map['level'][$value['level']] : $value['level'];

            $res = $this -> save($value);
            if(!$res instanceof Frameworkdetails){
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
}