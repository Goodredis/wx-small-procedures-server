<?php

namespace App\Repositories\Contracts;

interface StaffRepository extends BaseRepository
{	

	public function importStaffInfos($file);

	public function getStaffInfoByNames($names);

	public function getStaffInfoByUids($ids);

}