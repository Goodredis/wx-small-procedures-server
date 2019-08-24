<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Storage format of date field
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 定义主键非自增
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
        'password',
        'gender',
        'mobile',
        'email',
        'avatar',
        'employee_number',
        'order',
        'ldap_id',
        'org_id',
        'status'
    ];

    /**
     * 获取组织部所
     */
    public function org(){
        return $this->belongsTo('App\Models\Org', 'org_id');
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    // jwt 需要实现的方法
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // jwt 需要实现的方法, 一些自定义的参数
    public function getJWTCustomClaims()
    {
        return [];
    }
}
