<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractorder extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contract_order';

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
        'framework_id',
        'status',
        'del_flag'
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

    public function frameworkInfo() {
        return $this->belongsTo(Framework::class, 'framework_id', 'id')
                    ->where('status', 1)
                    ->where('del_flag', 0);
    }
    
}
