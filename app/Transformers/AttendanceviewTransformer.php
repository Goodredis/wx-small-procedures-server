<?php

namespace App\Transformers;

use App\Models\Attendanceview;
use League\Fractal\TransformerAbstract;

class AttendanceviewTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['staff'];

    public function transform(Attendanceview $attendanceview) {
        $formattedAttendanceview = [
            'random'                => str_random('32'),
            'workdate'              => strtotime($attendanceview->workdate),
            'checkin_remark'        => $attendanceview->checkin_remark,
            'checkout_remark'       => $attendanceview->checkout_remark,
            'checkin_position'      => $attendanceview->checkin_position,
            'checkout_position'     => $attendanceview->checkout_position,
            'checkin_at'            => intval($attendanceview->checkin_at),
            'checkout_at'           => intval($attendanceview->checkout_at),
            'checkin_source'        => intval($attendanceview->checkin_source),
            'checkout_source'       => intval($attendanceview->checkout_source),
            'checkin_source_flag'   => intval($attendanceview->checkin_source_flag),
            'checkout_source_flag'  => intval($attendanceview->checkout_source_flag)
        ];
        return $formattedAttendanceview;
    }

    public function includeStaff(Attendanceview $attendanceview) {
        $staffTransformer = new StaffTransformer();
        $staffTransformer = $staffTransformer->setDefaultIncludes(['company']);
        return $this->item($attendanceview->staff, $staffTransformer);
    }
}