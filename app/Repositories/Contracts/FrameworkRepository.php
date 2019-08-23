<?php //app/Repositories/Contracts/UserRepository.php

namespace App\Repositories\Contracts;

interface FrameworkRepository  extends BaseRepository
{
	/**
     * @brief  导入框架基本信息
     * @param $file 上传的文件
     */
    public function importFrameworkBasicInfo($file);

    /**
     * @brief  通过框架名称获取框架基本信息
     * @param  string names 多个用逗号隔开
     * @return array
     */
    public function getFrameworkInfoByNames($names);
}