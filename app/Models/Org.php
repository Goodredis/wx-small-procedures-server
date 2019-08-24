<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Org extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orgs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'order',
        'status',
    ];

    /**
     * 设置时间格式
     */
    protected $casts = [
        'created_at' => 'timestamps',
        'updated_at' => 'timestamps'
    ];
}
