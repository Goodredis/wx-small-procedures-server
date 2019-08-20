<?php

namespace App\Transformers;

use App\Models\Attendanceview;
use League\Fractal\TransformerAbstract;

class AttendanceviewTransformer extends TransformerAbstract
{
    public function transform(Attendanceview $attendanceview) {
        $formattedAttendanceview = [
            'staff'                 => [
                    'uid'                 => $attendanceview->staff->uid,
                    'name'                => $attendanceview->staff->name,
                    'mobile'              => $attendanceview->staff->mobile,
                    'email'               => $attendanceview->staff->email,
                    'employee_number'     => $attendanceview->staff->employee_number,
                    'company'             => $attendanceview->staff->companydetails,
                    'position'            => $attendanceview->staff->position,
                    'status'              => $attendanceview->staff->status
                ],
            'workdate'              => strtotime($attendanceview->workdate),
            'checkin_remark'        => $attendanceview->checkin_remark,
            'checkout_remark'       => $attendanceview->checkout_remark,
            'checkin_position'      => $attendanceview->checkin_position,
            'checkout_position'     => $attendanceview->checkout_position,
            'checkin_at'            => intval($attendanceview->checkin_at),
            'checkout_at'           => intval($attendanceview->checkout_at),
            'checkin_source'        => $attendanceview->checkin_source,
            'checkout_source'       => $attendanceview->checkout_source,
            'checkin_source_flag'   => $attendanceview->checkin_source_flag,
            'checkout_source_flag'  => $attendanceview->checkout_source_flag
        ];
        return $formattedAttendanceview;
    }
}