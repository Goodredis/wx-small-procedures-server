<?php

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\AttendanceRepository;

class EloquentAttendanceRepository extends AbstractEloquentRepository implements AttendanceRepository
{

	/*
     * @inheritdoc
     */
    public function save(array $data, $generateUidFlag = true) {
    	$data['del_flag'] = 0;
        return parent::save($data, $generateUidFlag);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        return parent::update($model, $data);
    }
    

	/**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = []) {
        return parent::findBy($searchCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id) {
        return parent::findOne($id);
    }
}