<?php

namespace App\Repositories;

use App\Models\Attendanceview;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\AttendanceviewRepository;

class EloquentAttendanceviewRepository extends AbstractEloquentRepository implements AttendanceviewRepository
{

	/**
     * 获取考勤list
     */
    public function getAttendanceviewList(array $searchCriteria = []) {
        $operatorCriteria = array();
        if (isset($searchCriteria['start_time']) && isset($searchCriteria['end_time'])) {
            $searchCriteria['workdate'] = date('Ymd', $searchCriteria['start_time']) . "~" . date('Ymd', $searchCriteria['end_time']);
            $operatorCriteria['workdate'] = 'between';
            unset($searchCriteria['start_time']); unset($searchCriteria['end_time']);
        }
        $params['orderby'] = isset($searchCriteria['orderby']) ? $searchCriteria['orderby'] : 'checkin_at ASC,checkout_at DESC';
        $params['page'] = isset($searchCriteria['page']) ? $searchCriteria['page'] : 1;
        $params['per_page'] = isset($searchCriteria['per_page']) ? $searchCriteria['per_page'] : 15;
        return parent::findBy($params, $operatorCriteria);
    }

}