<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model 
{
    /**
     * 定义数据表名
     *
     * @var string
     */
    protected $table = 'option';

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
        'key',           //'字典名称',
        'value',         //'字典对应的值',
        'description',   //'配置说明',
        'created_at',    //'字典创建日期',
        'updated_at'     //'更新时间',
    ];
}
