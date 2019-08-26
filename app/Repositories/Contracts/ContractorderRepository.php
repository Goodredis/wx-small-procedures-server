<?php

namespace App\Repositories\Contracts;

interface ContractorderRepository extends BaseRepository
{

	/**
	 * @brief  获取合同订单list
	 * @param    dept_id          部门
	 * @param  	 name             订单名称
	 * @param    code             订单编号
	 * @param    supplier_code    供应商
	 * @param    project_id       项目名称
	 * @param    project_id       项目编号
	 * @param    status           订单状态
	 * @return   collection
	 */
	public function getContractOrderInfos(array $searchCriteria = []);

	/**
	 * @brief  获取单条合同订单
	 * @param    id               订单id
	 * @return   collection
	 */
	public function getContractOrderInfoById($id);

	/**
     * @brief  导入合同订单信息
     * @param $file 上传的文件
     * 如果上传的文件名有append则是增量导入，否则是覆盖导入
     */
    public function importContractOrderInfo($file);

}