<?php
/**
 * Created by PhpStorm.
 * User: sinre
 * Date: 2016/10/25
 * Time: 18:16
 */

namespace org\wechat;


class TPWechat extends Wechat
{

    
    /**
     * @param $cache_name
     * @param $value
     * @param $expires
     * @return mixed
     */
    public function setCache($cache_name,$value,$expires)
    {
        return cache($cache_name,$value,$expires);
    }

    /**
     * @param $cache_name
     * @return mixed
     */
    public function getCache($cache_name)
    {
       return cache($cache_name);
    }

    /**
     * @param $cache_name
     * @return mixed
     */
    public function removeCache($cache_name)
    {
        return cache($cache_name,null);
    }
}