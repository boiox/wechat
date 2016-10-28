<?php
/**
 *
 */

namespace extend\Wechat;

class Wechat
{
    const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com';                    //api基础URL
    const AUTH_URL = '/cgi-bin/token?grant_type=client_credential&';            //获取access_token地址

    const OAUTH_PREFIX = 'https://open.weixin.qq.com';                          //oauth2认证基础URL
    const OAUTH_AUTHORIZE_URL = '/connect/oauth2/authorize?';                   //oauth2授权地址

    const OAUTH_TOKEN_URL = '/sns/oauth2/access_token?';                        //获取oauth2认证的access_token地址
    const OAUTH_USERINFO_URL = '/sns/userinfo?';                                //获取oauth2认证的用户信息
    const OAUTH_REFRESHTOKEN_URL = '/sns/oauth2/refresh_token?';                //刷新oauth2认证的access_token地址
    const OAUTH_AUTHTOKEN_URL = '/sns/auth?';                                   //验证oauth2的access_token的有效性

    private $appid;                     //appid
    private $appsecret;                 //appsecret
    private $access_token;
    private $user_token;                //通过code获取的access_token(和基础的access_token不是一回事)
    private $errCode;                   //错误码
    private $errMsg;                    //错误信息
    public function __construct($options)
    {
        $this->appid    = isset($options['appid']) ? $options['appid'] : '';
        $this->appsecret   = isset($options['appsecret']) ? $options['appsecret'] : '';
    }


    /**
     * 获取access_token
     * @return bool
     */
    public function getToken()
    {
        $cache_name = 'access_token_' . $this->appid;
        if($cache = $this->getCache($cache_name)){
            $this->access_token = $cache;
            return $cache;
        }

        $result = $this->http_get(self::API_BASE_URL_PREFIX . self::AUTH_URL . "appid=" . $this->appid . "&secret=" . $this->appsecret);
        if($result){
            $json = json_decode($result);
            if($json){
                if(isset($json['errcode'])){
                    $this->errCode = $json['errcode'];
                    $this->errMsg  = $json['errmsg'];
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

    /**
     * 授权请求地址，获得code
     * @param $callback                 回调地址
     * @param string $state             自定义参数值
     * @param string $scope             拉去授权方式 snsapi_base => 不弹出授权页面（静默拉取）   snsapi_userinfo => 弹出授权页面
     * @return string
     */
    public function getOauthUrl($callback,$state='',$scope='snsapi_base')
    {
        return self::OAUTH_PREFIX . self::OAUTH_AUTHORIZE_URL . 'appid=' . $this->appid . '&redirect_uri=' . $callback . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
    }

    /**
     * 获取Oauth的AccessToken（默认拉取用户的openid）
     * @return bool
     */
    public function getOauthAccessToken()
    {
        $code = $_GET['code'] ? $_GET['code'] : '';
        if(!$code) return false;

        $result = http_get(self::API_BASE_URL_PREFIX . self::OAUTH_TOKEN_URL . 'appid=' . $this->appid . '&secret=' . $this->appsecret . '&code=' . $code . '&grant_type=authorization_code');

        if($result){
            $json = json_decode($result);
            if(isset($json['errcode'])){
                $this->errCode = $json('errcode');
                $this->errMsg  = $json('errmsg');
                return false;
            }

            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }

    /**
     * 获取Oauth的用户信息
     * @param $access_token         //该access_token 是 Oauth2的access_token (getOauthAccessToken方法中获取的)
     * @param $openid
     * @return bool
     */
    public function getOauthUserInfo($access_token,$openid)
    {
        if(!$openid || !$access_token) return false;

        $result = http_get(self::API_BASE_URL_PREFIX . self::OAUTH_USERINFO_URL . 'access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN');

        if($result){
            $json = json_decode($result);
            if(isset($json['errcode'])){
                $this->errCode = $json('errcode');
                $this->errMsg  = $json('errmsg');
                return false;
            }

            return $json;
        }
        return false;
    }

    /**
     * 使用refresh_token 刷新 oauth的access_token
     * @param $refresh_token             //从getOauthAccessToken接口中获得
     * @return bool
     */
    public function refreshOauthAccessToken($refresh_token)
    {
        if(!$refresh_token) return false;
        $result = http_get(self::API_BASE_URL_PREFIX . self::OAUTH_REFRESHTOKEN_URL . 'appid=' . $this->appid . '&grant_type=refresh_token' . '&refresh_token=' . $refresh_token);

        if($result){
            $json = json_decode($result);
            if(isset($json['errcode'])){
                $this->errCode = $json('errcode');
                $this->errMsg  = $json('errmsg');
                return false;
            }

            $this->user_token = $json['access_token'];
            return $json;
        }
        return false;
    }

    /**
     * 验证oauth的access_token的有效性
     * @param $access_token
     * @param $openid
     * @return bool
     */
    public function authOauthAccessToken($access_token,$openid)
    {
        if(!$access_token || !$openid) return false;

        $result = http_get(self::API_BASE_URL_PREFIX . self::OAUTH_AUTHTOKEN_URL . 'access_token=' . $access_token .'&openid=' . $openid);

        if($result){
            $json = json_decode($result);
            if(isset($json['errcode'])){
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;

            }elseif($json['errmsg'] == 'ok') return true;
        }
        return false;
    }
    
}