<?php //app/Repositories/Contracts/UserRepository.php

namespace App\Repositories\Contracts;

interface SupplierRepository extends BaseRepository
{
	/**
     * @brief 导入厂商信息
     * @param $file 上传的文件
     */
    public function importSupplierBasicInfo($file);

    /**
     * @brief  通过厂商名称获取厂商基本信息
     * @param  string|array names 厂商名称
     * @return array
     */
    public function getSupplierInfoByNames($names);

    /**
     * @brief 获取厂商的字典，只包含简单的信息id，name，code
     * @return array
     */
    public function getSupplierDictionary();
}