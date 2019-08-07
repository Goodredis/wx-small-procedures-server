<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * 定义数据表名
     *
     * @var string
     */
    protected $table = 'framework_contract_details';

    /**
     * 定义created_at与updated_at不自动管理
     */
    public $timestamps = false;

    /**
     * 定义可操作的字段
     *
     * @var array
     */
    protected $fillable = [
        'framework_id',    //'合同框架表的id',
        'tax_ratio',       //税率',
        'price',           //'单价',
        'price_with_tax',  //'税后单价',
        'type',            //'类型，1开发，2测试',
        'level' ,          //'职级，1初级，2中级，3高级',
        'created_at',      //'创建时间',
        'updated_at',      //'修改时间',
    ];

    /**
     * 获取框架合同信息
     */
    public function framework()
    {
        return $this->belongsTo('App\Models\Framework', 'id', 'framework_id');
    }
}
