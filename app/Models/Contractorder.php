<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractorder extends Model
{

    /**
     * 合同订单表
     *
     * @var string
     */
    protected $table = 'contract_order';

    /**
     * 定义自动更新为时间戳格式
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 定义主键非自增
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * 定义Model返回的Columns
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'code',
        'dept_id',
        'signer',
        'project_id',
        'parent_project_id',
        'start_date',
        'end_date',
        'tax_ratio',
        'price',
        'price_with_tax',
        'supplier_code',
        'framework_id',
        'status',
        'del_flag'
    ];

    /**
     * 定义Columns的默认值
     *
     * @var array
     */
    protected $attributes = [
        'status'   => 1,
        'del_flag' => 0
    ];

    /**
     * 定义反向关联关系
     */
    public function frameworkInfo() {
        return $this->belongsTo(Framework::class, 'framework_id', 'id')
                    ->where('status', 1)
                    ->where('del_flag', 0);
    }
    
}
