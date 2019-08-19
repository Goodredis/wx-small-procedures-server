<?php //app/Repositories/Contracts/UserRepository.php

namespace App\Repositories\Contracts;

interface SupplierRepository extends BaseRepository
{
	/**
     * 导入厂商信息
     * @param $file 上传的文件
     */
    public function importSupplierBasicInfo($file);
}