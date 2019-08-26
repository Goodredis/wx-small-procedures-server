<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attendance';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'remark',
        'position',
        'purpose',
        'workdate',
        'check_at',
        'source',
        'source_flag',
        'del_flag',
    ];

    /**
     * set default value of column
     *
     * @var array
     */
    protected $attributes = [
        'del_flag' => 0
    ];

    public function staff() {
        return $this->belongsTo(Staff::class, 'uid', 'uid');
    }
    
}
