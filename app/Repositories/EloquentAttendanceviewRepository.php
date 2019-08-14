<?php

namespace App\Repositories;

use App\Models\Attendanceview;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\AttendanceviewRepository;

class EloquentAttendanceviewRepository extends AbstractEloquentRepository implements AttendanceviewRepository
{

    /**
     * @inheritdoc
     */
    public function getAttendanceviewItem(array $searchCriteria = []) {
        return parent::findOneBy($searchCriteria);
    }

	/**
     * @inheritdoc
     */
    public function getAttendanceviewList(array $searchCriteria = [], $operatorCriteria = []) {
        $params = array();
        if (isset($searchCriteria['uid'])) {
            $params['uid'] = trim($searchCriteria['uid']);
        }
        if (isset($searchCriteria['start_time']) && isset($searchCriteria['end_time'])) {
            $params['workdate'] = date('Ymd', $searchCriteria['start_time']) . "~" . date('Ymd', $searchCriteria['end_time']);
            $operatorCriteria['workdate'] = 'between';
        }
        $params['orderby'] = isset($searchCriteria['orderby']) ? $searchCriteria['orderby'] : 'checkin_at ASC,checkout_at DESC';
        $params['page'] = isset($searchCriteria['page']) ? $searchCriteria['page'] : 1;
        $params['per_page'] = isset($searchCriteria['per_page']) ? $searchCriteria['per_page'] : 15;
        return parent::findBy($params, $operatorCriteria);
    }

}