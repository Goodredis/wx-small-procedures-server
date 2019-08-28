<?php
/**
 * 重写transformer的返回格式，来控制是否有‘data’层
 */
namespace App\Foundations\Fractal;

use League\Fractal\Serializer\ArraySerializer;

class NoDataArraySerializer extends ArraySerializer
{
    /**
     * @brief 控制transformer返回值'data'是否显示
     * @param string resourceKey，
     * 如果为include则不显示data层
     * 如果为空则显示data层
     * 如果既不为空又不为include，则以$resourceKey指定的字符串做为键名返回数据
     */
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey) {
            return $resourceKey == 'include' ? $data : [$resourceKey => $data];
        }
        return ['data' => $data];
    }

    /**
     * @brief 控制transformer返回值'data'是否显示
     * @param string resourceKey，
     * 如果为include则不显示data层
     * 如果为空则显示data层
     * 如果既不为空又不为include，则以$resourceKey指定的字符串做为键名返回数据
     */
    public function item($resourceKey, array $data)
    {

        if ($resourceKey) {
            return $resourceKey == 'include' ? $data : [$resourceKey => $data];
        }
        return ['data' => $data];
    }
}