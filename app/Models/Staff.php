<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'staff';

    /**
     * 设置时间格式
     */
    protected $casts = [
        'created_at' => 'timestamps',
        'updated_at' => 'timestamps'
    ];

    /**
     * Storage format of date field
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'gender',
        'level',
        'mobile',
        'email',
        'birthday',
        'idcard',
        'password',
        'employee_number',
        'company',
        'position',
        'type',
        'label',
        'status',
        'del_flag',
        'highest_education',
        'university',
        'major'
    ];

    /**
     * set default value of column
     *
     * @var array
     */
    protected $attributes = [
        'status'   => 1,
        'del_flag' => 0
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function companydetails() {
        return $this->belongsTo(Supplier::class, 'company', 'code');
    }
    
}
