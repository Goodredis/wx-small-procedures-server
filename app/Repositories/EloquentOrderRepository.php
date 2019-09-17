<?php
/**
 * Created by PhpStorm.
 * User: w17600101602
 * Date: 2019/9/17
 * Time: 11:05
 */

namespace App\Repositories;

use App\Repositories\Contracts\OrderRepository;

class EloquentOrderRepository extends AbstractEloquentRepository implements OrderRepository
{
    public function findBy(array $searchCriteria = [], array $operatorCriteria = [])
    {
        return parent::findBy($searchCriteria, $operatorCriteria);
    }


}