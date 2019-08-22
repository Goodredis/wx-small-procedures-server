<?php

namespace App\Repositories\Contracts;

interface ContractorderRepository extends BaseRepository
{

	public function getContractOrderInfos(array $searchCriteria = []);

	public function getContractOrderInfoById($id);

}