<?php

namespace App\Repositories;

use Exception;
use App\Utils\Excel;
use App\Models\Staff;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\AttendanceRepository;

class EloquentAttendanceRepository extends AbstractEloquentRepository implements AttendanceRepository
{

    /*
     * @inheritdoc
     */
    public function save(array $data) {
        // 判断该用户是否存在
        $eloquentStaffRepository = new EloquentStaffRepository(new Staff());
        $userInfo = $eloquentStaffRepository->getStaffInfoByUids($data['uid']);
        if (empty($userInfo)) {
            throw new Exception(trans('errorCode.120001'), 120001);
        }
        // 补卡必须填写补卡说明
        if ($data['source_flag']==2 && empty($data['remark'])) {
            throw new Exception(trans('errorCode.120002'), 120002);
        }
        // 设置打卡日期
        $data['workdate'] = date('Ymd', $data['check_at']);
        return parent::save($data);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        // 判断该用户是否存在
        $eloquentStaffRepository = new EloquentStaffRepository(new Staff());
        $userInfo = $eloquentStaffRepository->getStaffInfoByUids($data['uid']);
        if (empty($userInfo)) {
            throw new Exception(trans('errorCode.120001'), 120001);
        }
        // 设置打卡日期
        $data['workdate'] = date('Ymd', $data['check_at']);
        return parent::update($model, $data);
    }
    

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], array $operatorCriteria = []) {
        // 检索公司
        // 检索起止日期
        if (isset($searchCriteria['start_time']) && isset($searchCriteria['end_time'])) {
            $searchCriteria['workdate'] = date('Ymd', $searchCriteria['start_time']) . "~" . date('Ymd', $searchCriteria['end_time']);
            $operatorCriteria['workdate'] = 'between';
            unset($searchCriteria['start_time']); unset($searchCriteria['end_time']);
        }
        // 检索状态
        // 检索项目名称
        // 检索项目编号
        // 检索人员 Automation
        $searchCriteria['del_flag'] = 1;
        $operatorCriteria['del_flag'] = '!=';
        $searchCriteria['orderby'] = isset($searchCriteria['orderby']) ? $searchCriteria['orderby'] : 'workdate DESC, check_at ASC';
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

    /**
     * @inheritdoc
     */
    public function getAttendanceItemById($id) {
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
            $attendance = $this -> getAttendanceItemById($id);
            $this -> delete($attendance);
        }
    }

    public function exportAttendances(array $export_data = []) {
        $lists = array();
        foreach ($export_data['data'] as $key => $value) {
            $lists[$key]['date'] = $value['workdate'];
            $lists[$key]['uid'] = $value['uid'];
            $lists[$key]['checkin_at'] = date('Y-m-d H:i:s', intval($value['checkin_at']));
            $lists[$key]['checkout_at'] = date('Y-m-d H:i:s', intval($value['checkout_at']));
            $lists[$key]['status'] = '正常';
        }
        $format_column = array('日期', '姓名', '上班时间', '下班时间', '状态');
        $filename = '外协人员考勤信息';
        Excel::export($lists, $format_column, $filename);
        exit;
    }

}
