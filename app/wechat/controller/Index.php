<?php

/**
 * Created by PhpStorm.
 * User: coolong
 * Date: 2016/10/25
 * Time: 10:40
 */
namespace app\wechat\controller;

use think\Controller;

class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
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