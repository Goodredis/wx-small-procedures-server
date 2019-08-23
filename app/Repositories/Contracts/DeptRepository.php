<?php //app/Repositories/Contracts/UserRepository.php

namespace App\Repositories\Contracts;

interface DeptRepository  extends BaseRepository
{
    /**
     * @brief  通过部所名称获取部所基本信息
     * @param  string|array names 部所名称
     * @return array
     */
    public function getDeptInfoByNames($names);

    /**
     * @brief导入部所基本信息
     * @param $file 上传的文件
     * 如果上传的文件名有append则是增量导入，否则是覆盖导入
     */
    public function importDeptInfo($file);

    /**
     * @brief 获取部所的字典，只包含简单的信息id，name，department_id
     * @param string name 模糊查询部所名
     * @return array
     */
    public function getDeptDictionary($name = '');
}