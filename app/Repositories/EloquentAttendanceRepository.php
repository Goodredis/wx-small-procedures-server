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
            $params['check_in_at'] = $searchCriteria['start_time'] . "~" . $searchCriteria['end_time'];
            $operatorCriteria['check_in_at'] = 'between';
        }
        if (isset($searchCriteria['orderby'])) {
            $params['orderby'] = $searchCriteria['orderby'];
        }
        return parent::findBy($params, $operatorCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id) {
        return parent::findOne($id);
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