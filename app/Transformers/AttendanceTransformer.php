<?php

namespace App\Transformers;

use App\Models\Attendance;
use League\Fractal\TransformerAbstract;

class AttendanceTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['staff'];

    public function transform(Attendance $attendance) {
        $formattedAttendance = [
            'id'                    => $attendance->id,
            'uid'                   => $attendance->uid,
            'remark'                => $attendance->remark,
            'position'              => $attendance->position,
            'purpose'               => intval($attendance->purpose),
            'workdate'              => strtotime($attendance->workdate),
            'check_at'              => intval($attendance->check_at),
            'source'                => intval($attendance->source),
            'source_flag'           => intval($attendance->source_flag),
            'created_at'            => strtotime($attendance->created_at),
            'updated_at'            => strtotime($attendance->updated_at),
            'del_flag'              => intval($attendance->del_flag)
        ];
        return $formattedAttendance;
    }

    public function includeStaff(Attendance $attendance) {
        return $this->item($attendance->staff, new StaffTransformer());
    }
}