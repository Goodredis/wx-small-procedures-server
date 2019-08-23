<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dept extends Model 
{
    /**
     * 定义数据表名
     *
     * @var string
     */
    protected $table = 'cmri_dept';

    /**
     * 定义可操作的字段
     *
     * @var array
     */
    protected $fillable = [
        'department_id', //'部门id',
        'name'           //'部门名称',
    ];
}
