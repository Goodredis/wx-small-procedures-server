<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendanceview extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attendance_view';

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
        'workdate',
        'checkin_remark',
        'checkout_remark',
        'checkin_position',
        'checkout_position',
        'checkin_at',
        'checkout_at',
        'checkin_source',
        'checkout_source',
        'checkin_source_flag',
        'checkin_source_flag',
    ];

    public function staff() {
        return $this->belongsTo(Staff::class, 'uid', 'id');
    }
    
}
