<?php

/**
 * Created by PhpStorm.
 * User: coolong
 * Date: 2016/10/25
 * Time: 10:40
 */
namespace app\wechat\controller;

use think\Controller;

use extend\Wechat;

class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //$this->token_verify();                  //验证token

//        $options['appid'] = "wxdcc677f0fab756ed";
//        $options['appsecret'] = "28e0f7453abe4872ea0a191f57795224";
//        $wechat = new TPWechat($options);

        

    }

    protected function token_verify()
    {
        $echoStr = input('echostr');
        $signature = input("signature");
        $timestamp = input("timestamp");
        $nonce = input("nonce");

        $token = 'wechat';
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return $echoStr;
        }else{
            return false;
        }
    }
}