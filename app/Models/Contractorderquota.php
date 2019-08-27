<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractorderquota extends Model
{

    /**
     * 合同订单配额表
     *
     * @var string
     */
    protected $table = 'contract_order_quota';

    /**
     * 设置时间格式
     */
    protected $casts = [
        'created_at' => 'timestamps',
        'updated_at' => 'timestamps'
    ];

    /**
     * 定义自动更新为时间戳格式
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 定义Model返回的Columns
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'contract_order_id',
        'signer',
        'project_id',
        'parent_project_id',
        'tax_ratio',
        'price',
        'price_with_tax',
        'status'
    ];

    /**
     * 定义Columns的默认值
     *
     * @var array
     */
    protected $attributes = [
        'status'   => 1
    ];
    
}
