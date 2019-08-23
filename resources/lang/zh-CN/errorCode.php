<?php

/**
 * 状态码说明
 * 	   以下状态码均为六位纯数字、从1开始、如新加模块请顺序添加
 *     公共模块        110001
 *     考勤		       120001
 *     人员		       130001
 *     框架合同		   140001
 *     厂商		       150001
 *     合同订单		   160001
 */
return [
    //上传文件类错误
    110001 => "文件类型不允许",
    110002 => "文件太大",
    110003 => "文件数据为空",
    110004 => "文件标题与字典不对应",
    110005 => "部分信息导入失败",
    120001 => "批量操作参数格式错误、请核对",
    130001 => "批量操作参数格式错误、请核对",
];
