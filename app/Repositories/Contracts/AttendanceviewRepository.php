<?php

namespace App\Repositories\Contracts;

interface AttendanceviewRepository extends BaseRepository
{
	
	public function getAttendanceviewItem(array $searchCriteria);

	public function getAttendanceviewList(array $searchCriteria);
}