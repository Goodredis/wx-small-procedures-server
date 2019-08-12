<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Framework extends Model 
{
    /**
     * 定义数据表名
     *
     * @var string
     */
    protected $table = 'framework_contract';

    /**
     * 定义主键非自增
     */
    public $incrementing = false;

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
        'id',            //'由code经过哈希算法生成的唯一标识',
        'name',          //'名称',
        'code',          //'合同编号',
        'start_date',    //'合同开始日期',
        'end_date',      //'合同截止日期',
        'type',          //'框架类型，1开发，2测试',
        'tax_ratio',     //'税率',
        'price',         //'不含税价款',
        'price_with_tax',//'含税价款',
        'supplier_code', //'供应商code',
        'status',        //'框架订单状态，1执行中，2已完成',
        'created_at',    //'合同创建日期',
        'updated_at',    //'更新时间',
        'del_flag'       //'是否删除，0未删除，1已删除',
    ];


    /**
     * 获取供应商
     */
    public function supplier(){
        return $this->belongsTo('App\Models\Supplier', 'supplier_code', 'code');
    }

    /**
     * 获取框架合同详情
     */
    public function frameworkdetails(){
        return $this->hasMany('App\Models\Frameworkdetails', 'framework_id', 'id')->orderBy('id','desc');
    }
}
