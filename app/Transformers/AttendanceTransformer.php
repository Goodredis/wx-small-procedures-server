<?php

namespace App\Transformers;

use App\Models\Attendance;
use League\Fractal\TransformerAbstract;

class AttendanceTransformer extends TransformerAbstract
{
    public function transform(Attendance $attendance) {
        $formattedAttendance = [
            'id'                    => $attendance->id,
            'uid'                   => $attendance->uid,
            'remark'                => $attendance->remark,
            'position'              => $attendance->position,
            'purpose'               => $attendance->purpose,
            'workdate'              => $attendance->workdate,
            'check_at'              => $attendance->check_at,
            'source'                => $attendance->source,
            'source_flag'           => $attendance->source_flag,
            'del_flag'              => $attendance->del_flag,
            'createdAt'             => (string) $attendance->created_at,
            'updatedAt'             => (string) $attendance->updated_at
        ];

        return $formattedAttendance;
    }
}