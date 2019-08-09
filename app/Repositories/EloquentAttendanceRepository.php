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
    public function findBy(array $searchCriteria = [], $operatorCriteria = [], $orderCriteria = 'created_at') {
        return parent::findBy($searchCriteria, $operatorCriteria, $orderCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id) {
        return parent::findOne($id);
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