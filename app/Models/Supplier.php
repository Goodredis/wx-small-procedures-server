<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
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
    public $timestamps = false;

    /**
     * 定义可操作的字段
     *
     * @var array
     */
    protected $fillable = [
        'name',        //'供应商名称',
        'code',        //'供应商编号',
        'created_at',  //'创建时间',
        'updated_at',  //'更新时间',
        'del_flag',    //'是否删除，0未删除，1已删除',
    ];

    /**
     * 获取厂商提供的框架合同
     */
    public function framwork()
    {
        return $this->hasMany('App\Models\Framework', 'supplier', 'code');
    }
}
