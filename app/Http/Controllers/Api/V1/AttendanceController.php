<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

class AttendanceController extends BaseController
{
	public function index()
	{
		return str_random('32');exit;
	}
}