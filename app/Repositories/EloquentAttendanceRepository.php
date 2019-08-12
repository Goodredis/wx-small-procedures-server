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
        $export_data = array(
            array(
                'date'      => '20190808',
                'name'      => 'test1',
                'check_in'  => '2019-08-08 08:37:28',
                'check_out' => '2019-08-08 18:07:56',
                'status'    => '正常',
            ),
            array(
                'date'      => '20190809',
                'name'      => 'test2',
                'check_in'  => '2019-08-09 08:36:28',
                'check_out' => '2019-08-09 18:06:56',
                'status'    => '正常',
            ),
            array(
                'date'      => '20190810',
                'name'      => 'test3',
                'check_in'  => '2019-08-10 08:35:28',
                'check_out' => '2019-08-10 18:05:56',
                'status'    => '正常',
            ),
            array(
                'date'      => '20190811',
                'name'      => 'test4',
                'check_in'  => '2019-08-11 08:33:28',
                'check_out' => '2019-08-11 18:03:56',
                'status'    => '正常',
            ),
        );
        $format_column = array('日期', '姓名', '上班时间', '下班时间', '状态');
        $filename = '外协人员考勤信息';
        parent::export($export_data, $format_column, $filename);
        exit;
    }

    public function getAttendancesByDate($uid, $date) {
        $start_time = strtotime(date('Y-m-d 00:00:00', strtotime($date)));
        $end_time = $start_time+60*60*24;
        $attendances = Attendance::select('id', 'uid', 'remark', 'position', 'purpose', 'check_in_at', 'source', 'source_flag')
                            ->where([['uid', $uid], ['del_flag', '!=', 1]])
                            ->whereBetween('check_in_at', [$start_time, $end_time])
                            ->orderBy('check_in_at', 'ASC')
                            ->get();
        return $attendances->toArray();
    }

    public function arrangeUpdateCheckinat($params) {
        $check_in_at = array();
        if(isset($params['check_in'])) {
            if(!strpos($params['check_in'], ',')){
                return false;
            }
            $arr = explode(',', $params['check_in']);
            $check_in_at[] = array('id' => current($arr), 'purpose' => 1, 'check_in_at' => end($arr));
        }
        if(isset($params['check_out'])) {
            if(!strpos($params['check_out'], ',')){
                return false;
            }
            $arr = explode(',', $params['check_out']);
            $check_in_at[] = array('id' => current($arr), 'purpose' => 2, 'check_in_at' => end($arr));
        }
        return $check_in_at;
    }
}