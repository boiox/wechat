<?php

/**
 * Created by PhpStorm.
 * User: sinre
 * Date: 2016/11/11
 * Time: 16:37
 */
namespace app\wechat\model;

use think\Model;

class WxUser extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'wx_user';
}