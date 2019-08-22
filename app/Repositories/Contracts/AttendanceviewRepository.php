<?php

namespace App\Repositories\Contracts;

interface AttendanceviewRepository extends BaseRepository
{

	public function getAttendanceviewList(array $searchCriteria = []);
	
}