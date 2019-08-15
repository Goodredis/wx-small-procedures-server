<?php

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\AttendanceRepository;

class EloquentAttendanceRepository extends AbstractEloquentRepository implements AttendanceRepository
{

	/*
     * @inheritdoc
     */
    public function save(array $data, $generateUidFlag = true) {
    	$data['del_flag'] = 0;
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
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = []) {
        $params = array();
        if (isset($searchCriteria['uid'])) {
            $params['uid'] = trim($searchCriteria['uid']);
        }
        if (isset($searchCriteria['start_time']) && isset($searchCriteria['end_time'])) {
            $params['check_at'] = date('Ymd', $searchCriteria['start_time']) . "~" . date('Ymd', $searchCriteria['end_time']);
            $operatorCriteria['check_at'] = 'between';
        }
        $params['orderby'] = isset($searchCriteria['orderby']) ? $searchCriteria['orderby'] : 'workdate DESC, check_at ASC';
        $params['page'] = isset($searchCriteria['page']) ? $searchCriteria['page'] : 1;
        $params['per_page'] = isset($searchCriteria['per_page']) ? $searchCriteria['per_page'] : 15;
        return parent::findBy($params, $operatorCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id) {
        return parent::findOne($id);
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

    public function exportAttendance(array $export_data = []) {
        $lists = array();
        foreach ($export_data['data'] as $key => $value) {
            $lists[$key]['date'] = $value['workdate'];
            $lists[$key]['uid'] = $value['uid'];
            $lists[$key]['checkin_at'] = date('Y-m-d H:i:s', $value['checkin_at']);
            $lists[$key]['checkout_at'] = date('Y-m-d H:i:s', $value['checkout_at']);
            $lists[$key]['status'] = '正常';
        }
        $format_column = array('日期', '姓名', '上班时间', '下班时间', '状态');
        $filename = '外协人员考勤信息';
        parent::export($lists, $format_column, $filename);
        exit;
    }

}