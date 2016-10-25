<?php
/**
 *
 */

namespace ectend\wechat;

class Wechat
{
    const API_BASE_URL = 'https://api.weixin.qq.com/cgi-bin';                   //基础接口URL
    const AUTH_URL = '/token?grant_type=client_credential&';                    //获取access_token地址


    private $token;
    private $access_token;
    private $errCode;                   //错误码
    private $errMsg;                    //错误信息
    public function __construct($options)
    {
        $this->token = isset($options['token']) ? $options['token'] : '';
    }


    /**
     * 获取access_token
     * @param string $appid
     * @param string $secret
     * @return bool
     */
    public function getToken($appid='',$secret='')
    {
        $cache_name = 'access_token_' . $appid;
        if($cache = $this->getCache($cache_name)){
            $this->access_token = $cache;
            return $cache;
        }

        $result = $this->http_get(self::API_BASE_URL.self::AUTH_URL."appid=".$appid."&secret=".$secret);
        if($result){
            $json = json_decode($result);
            if($json){
                if($json['errcode']){
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }

                $this->access_token = $json['access_token'];
                $expires = $json['expires_in'] ? $json['expires_in']-50 : 7000;
                $this->setCache($cache_name,$this->access_token,$expires);

                return $this->access_token;
            }
        }
        return false;
    }


    /**
     * GET 请求
     * @param $url
     * @return bool
     */
    private function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    private function http_post($url,$param,$post_file=false){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * 设置缓存，需要重载
     * @param $cache_name
     * @param $value
     * @param $expires
     * @return bool
     */
    protected function setCache($cache_name,$value,$expires)
    {
        //TODO: 设置缓存
        return false;
    }

    /**
     * 获取缓存，需要重载
     * @param $cache_name
     * @return bool
     */
    protected function getCache($cache_name)
    {
        //TODO: 获取缓存
        return false;
    }

    /**
     * 清除缓存，需要重载
     * @param $cache_name
     * @return bool
     */
    protected function removeCache($cache_name)
    {
        //TODO: 清除缓存
        return false;
    }

}