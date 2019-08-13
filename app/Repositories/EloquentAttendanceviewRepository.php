<?php

namespace App\Repositories;

use App\Models\Attendanceview;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\AttendanceviewRepository;

class EloquentAttendanceviewRepository extends AbstractEloquentRepository implements AttendanceviewRepository
{

	/**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = []) {
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id) {
        return parent::findOne($id);
    }

}