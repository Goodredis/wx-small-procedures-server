<?php

namespace App\Repositories\Contracts;

interface ContractorderquotaRepository extends BaseRepository
{

	/**
     * 合同订单配额分配(全量)
     * @param  string $id
     * @param  array  $criteria
     * @return
     */
	public function assignOrderToProjects($id, array $criteria);

}