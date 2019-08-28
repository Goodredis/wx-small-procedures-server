<?php

namespace App\Repositories\Contracts;

interface ContractorderquotaRepository extends BaseRepository
{

	/**
     * @brief 获取合同订单项目记录
     * @param Request $request
     * @param string  $id
     * @return collection
     */
	public function getProjectsFromOrder($id, array $criteria = []);

	/**
     * @brief 合同订单配额分配(全量)
     * @param  string $id
     * @param  array  $criteria
     * @return
     */
	public function assignOrderToProjects($id, array $criteria);

}