<?php

namespace App\Tools;

/**
 * File class
 * @author wanggang
 * @version 1.0
 * @time 2019-08-21
 */
class File
{

	/**
     * 上传文件，并返回文件的保存地址
     * @param    file     file
     * @return   string   filename
     */
    public static function upload($file) {
        //获取上传文件的类型
        $fileextension = $file -> getClientOriginalExtension();
        $allow_type = explode(",", env('UPLOAD_FILE_TYPE'));
        if(!in_array($fileextension, $allow_type)){
            return ['err_code' => 40001];
        }
        //获取上传文件的大小
        $filesize=$file->getClientSize();
        if($filesize > env('UPLOAD_MAX_SIZE')*1024*1024){
            return ['err_code' => 40002];
        }
        //获取文件名称,并整理新名称，新名称规则为[filename+时间.扩展名]
        $filename = $file->getClientOriginalName();
        $filename = explode('.', $filename);
        $savename = $filename[0] . date('YmdHis', time()) . '.' . $filename[1];
        //将文件放到上传文件的目录
        $path = $file->move(env('UPLOAD_DIR'),$savename);
        return $path->getPathName();
    }

}
