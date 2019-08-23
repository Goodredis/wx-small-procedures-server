<?php

namespace App\Repositories;

use App\Tools\File;
use App\Tools\Excel;
use Ramsey\Uuid\Uuid;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\StaffRepository;

class EloquentStaffRepository extends AbstractEloquentRepository implements StaffRepository
{

    // 导入字典
    private $format_column = array(
        '姓名'            =>     'name',
        '性别'            =>     'gender',
        '职级'            =>     'level',
        '手机号'          =>     'mobile',
        '邮箱'            =>     'email',
        '出生日期'        =>     'birthday',
        '身份证号'        =>     'idcard',
        '登录密码'        =>     'password',
        '员工编号'        =>     'employee_number',
        '公司名称'        =>     'company',
        '职位'            =>     'position',
        '人员类型'        =>     'type',
        '人员标签'        =>     'label',
        '人员状态'        =>     'status',
        '最高学历'        =>     'highest_education',
        '毕业院校'        =>     'university',
        '毕业专业'        =>     'major',
        '专业类型'        =>     'major_type',
        '专业等级'        =>     'major_level'
    );

	/*
     * @inheritdoc
     */
    public function save(array $data, $generateUidFlag = true) {
        $data['uid'] = Uuid::uuid4();
        $data['birthday'] = date('Ymd', $data['birthday']);
        return parent::save($data, $generateUidFlag);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        return parent::update($model, $data);
    }
    
    /**
     * @brief  通过名字获取用户信息
     * @param  string  多个用逗号隔开
     * @return array
     */
    public function getStaffInfoByNames($names) {
        $staffs = parent::findBy(array('name' => $names, 'del_flag' => 0))->toArray();
        return !strpos($names, ",") ? array_pop($staffs['data']) : $staffs['data'];
    }

    /**
     * @brief  通过ids获取用户信息
     * @param  string | array
     * @return array
     */
    public function getStaffInfoByUids($ids) {
        $flag = true;
        if (is_array($ids)) {
            if (count($ids) > 1) {
                $ids = implode(",", $ids);
                $flag = false;
            } else {
                $ids = array_pop($ids);
            }
        }
        $staffs = parent::findBy(array('uid' => $ids, 'del_flag' => 0))->toArray();
        return ($flag == true) ? array_pop($staffs['data']) : $staffs['data'];
    }

	/**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = []) {
        if (isset($searchCriteria['name'])) {
            $searchCriteria['name'] = '%' . $searchCriteria['name'] . '%';
            $operatorCriteria['name'] = 'like';
        }
        if (isset($searchCriteria['label'])) {
            $label = '';
            $labelData = explode(',', $searchCriteria['label']);
            if (count($labelData) === 1) {
                $label = "FIND_IN_SET('".$labelData[0]."', `label`)";
            } else {
                for ($i=0; $i < count($labelData); $i++) { 
                    $label .= "FIND_IN_SET('".$labelData[$i]."', `label`) OR ";
                }
                $label = "(" . substr($label, 0, -4) . ")";
            }
            $searchCriteria['label'] = $label;
            $operatorCriteria['label'] = 'raw';
        }
        $searchCriteria['del_flag'] = 0;
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

    /**
     * @inheritdoc
     */
    public function getStaffItemById($id) {
        $criteria = array('id' => $id, 'del_flag' => 0);
        return parent::findOneBy($criteria);
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model){
        return parent::update($model, ['del_flag' => 1]);
    }

    /**
     * @inheritdoc
     */
    public function destroy($ids){
        foreach ($ids as $key => $id) {
            $attendance = $this -> findOne($id);
            $this -> delete($attendance);
        }
    }

    public function dictionary($keyword) {
        $staffs = Staff::select('uid', 'name')
                        ->where('name', 'like', '%'.$keyword.'%')
                        ->where('status', '=', 1)
                        ->where('del_flag', '=', 0)
                        ->orderBy('created_at', 'DESC')
                        ->get();
        return empty($staffs) ? array() : $staffs;
    }

    public function importStaffInfos($file) {
        $file_path = File::upload($file);

        $data = Excel::import($file_path, $this -> format_column);

        $error_data = array();
        foreach ($data as $key => $value) {
            $value = $this->filterImportData($value);
            $res = $this -> save($value);
            if(!$res instanceof Staff){
                $error_data['create_failed'][] = $value;
            }
        }
        if(!empty($error_data)){
            return ['err_code' => 11005, 'error_data' => $error_data];
        }
        //删除文档
        @unlink($file_path);
        return true;
    }

    private function filterImportData($data) {
        if(isset($data['gender'])) {
            $data['gender'] = $data['gender']=='男' ? 1 : 2;
        }
        if(isset($data['type'])) {
            $data['type'] = $data['type']=='开发' ? 1 : 2;
        }
        if(isset($data['status'])) {
            $data['status'] = $data['status']=='在职' ? 1 : 2;
        }
        $level = '';
        switch ($data['level']) {
            case '初级':
                $level = 1;
                break;
            case '中级':
                $level = 2;
                break;
            case '高级':
                $level = 3;
                break;
        }
        $data['level'] = $level;
        $data['birthday'] = isset($data['birthday']) ? strtotime($data['birthday']) : '';
        $data['company']  = isset($data['company'])  ? '351b1281-763e-41d9-a5e0-1b6ddd16ecdd' : '';
        $data['position'] = isset($data['position']) ? 'manager' : '';
        return $data;
    }

}