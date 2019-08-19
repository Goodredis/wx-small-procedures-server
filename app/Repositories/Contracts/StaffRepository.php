<?php

namespace App\Repositories\Contracts;

interface StaffRepository extends BaseRepository
{	

	public function importStaffInfos($file);

}