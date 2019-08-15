<?php //app/Repositories/Contracts/UserRepository.php

namespace App\Repositories\Contracts;

interface FrameworkdetailsRepository  extends BaseRepository
{
	/**
     * 导入框架基本详情信息
     * @param $file 上传的文件
     */
    public function importDetailInfo($file);
}