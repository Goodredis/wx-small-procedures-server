<?php

namespace App\Repositories\Contracts;

interface AttendanceRepository extends BaseRepository
{

	public function getAttendanceItemById($id);

	public function exportAttendances(array $export_data = []);
	
}