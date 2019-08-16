<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /**
     * 定义数据库表名
     *
     * @var string
     */
    protected $table = 'supplier';

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
        'name',        //'供应商名称',
        'code',        //'供应商编号',
        'status',      //供应商状态，1正常，2禁用
        'created_at',  //'创建时间',
        'updated_at',  //'更新时间',
        'del_flag'    //'是否删除，0未删除，1已删除',
    ];

    /**
     * 定义默认字段
     *
     * @var array
     */
    protected $attributes = [
        'del_flag' => 0,
        'status' => 1
    ];

    /**
     * 获取厂商提供的框架合同
     */
    public function framework()
    {
        return $this->hasMany('App\Models\Framework', 'supplier_code', 'code')
        -> where('del_flag',0)
        -> orderBy('created_at', 'desc');
    }
}
