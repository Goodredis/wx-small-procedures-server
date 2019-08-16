<?php

namespace App\Repositories;

use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\StaffRepository;

class EloquentStaffRepository extends AbstractEloquentRepository implements StaffRepository
{

	/*
     * @inheritdoc
     */
    public function save(array $data, $generateUidFlag = true) {
        $data['uid'] = Uuid::uuid4();
        $data['birthday'] = date('Ymd', $data['birthday']);
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
    public function findBy(array $searchCriteria = [], $operatorCriteria = []) {
        if (isset($searchCriteria['name'])) {
            $searchCriteria['name'] = '%' . $searchCriteria['name'] . '%';
            $operatorCriteria['name'] = 'like';
        }
        if (isset($searchCriteria['label'])) {
            $label = '';
            $labelData = explode(',', $searchCriteria['label']);
            if (count($labelData) === 1) {
                $label = "FIND_IN_SET('".$labelData[0]."', `label`)";
            } else {
                for ($i=0; $i < count($labelData); $i++) { 
                    $label .= "FIND_IN_SET('".$labelData[$i]."', `label`) OR ";
                }
                $label = "(" . substr($label, 0, -4) . ")";
            }
            $searchCriteria['label'] = $label;
            $operatorCriteria['label'] = 'raw';
        }
        return parent::findBy($searchCriteria, $operatorCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id) {
        return parent::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model){
        return parent::update($model, ['del_flag' => 1]);
    }

    /**
     * @inheritdoc
     */
    public function destroy($ids){
        foreach ($ids as $key => $id) {
            $attendance = $this -> findOne($id);
            $this -> delete($attendance);
        }
    }

}