<?php
/**
 * Created by PhpStorm.
 * User: w17600101602
 * Date: 2019/9/17
 * Time: 11:13
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order_info';

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
     * 定义默认字段
     *
     * @var array
     */
    protected $attributes = [
        'del_flag' => 0,
        'status' => 'CT'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'oid',
        'type',
        'status',
        'del_flag'
    ];

}