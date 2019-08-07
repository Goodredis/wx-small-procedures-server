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
        'id',
        'uid',
        'remark',
        'position',
        'purpose',
        'check_in_at',
        'source',
        'source_flag',
        'del_flag',
    ];
    
}
