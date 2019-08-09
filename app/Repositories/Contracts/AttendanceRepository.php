<?php

namespace App\Repositories\Contracts;

interface AttendanceRepository extends BaseRepository
{
	public function getAttendancesByDate($uid, $date);
	public function arrangeUpdateCheckinat($params);
}