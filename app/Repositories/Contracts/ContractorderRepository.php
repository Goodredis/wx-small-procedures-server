<?php

namespace App\Repositories\Contracts;

interface ContractorderRepository extends BaseRepository
{

	public function getContractOrderInfos(array $searchCriteria = []);

	public function getContractOrderInfoById($id);

	/**
     * @brief  导入合同订单信息
     * @param $file 上传的文件
     * 如果上传的文件名有append则是增量导入，否则是覆盖导入
     */
    public function importContractOrderInfo($file);

}