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

    /**
     * @brief 获取框架的字典，只包含简单的信息id，name，code
     * @param string name 模糊查询厂商名
     * @return array
     */
    public function getFrameworkDictionary($name = '');

    /**
     * @brief  通过框架编号获取框架基本信息
     * @param  string|array names 框架编号
     * @return array
     */
    public function getFrameworkInfoByCodes($codes);
}