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
        $searchCriteria['orderby'] = isset($searchCriteria['orderby']) ? $searchCriteria['orderby'] : 'workdate DESC, checkin_at ASC, checkout_at DESC';
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

}