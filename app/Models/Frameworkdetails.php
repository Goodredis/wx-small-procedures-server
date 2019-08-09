<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frameworkdetails extends Model
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
    // public $timestamps = false;
    
    /**
     * 设置时间格式
     */
    protected $casts = [
        'created_at' => 'timestamps',
        'updated_at' => 'timestamps'
    ];

    /**
     * 模型的日期字段保存格式，时间戳
     *
     * @var string
     */
    protected $dateFormat = 'U';

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
        return $this->belongsTo('App\Models\Framework', 'framework_id', 'id');
    }
}
