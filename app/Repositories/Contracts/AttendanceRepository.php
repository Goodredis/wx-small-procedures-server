<?php

namespace App\Repositories\Contracts;

interface AttendanceRepository extends BaseRepository
{

	public function exportAttendance(array $export_data = []);
	
}