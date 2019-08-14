<?php //app/Repositories/Contracts/UserRepository.php

namespace App\Repositories\Contracts;

interface FrameworkRepository  extends BaseRepository
{
	/**
     * 导入框架基本信息
     * @param $file 上传的文件
     */
    public function importBasicInfo($file);
}