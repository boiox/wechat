<?php

/**
 * Created by PhpStorm.
 * User: coolong
 * Date: 2016/10/25
 * Time: 10:40
 */
namespace app\wechat\controller;

use think\Controller;

use org\wechat\TPWechat;

class Index extends Controller
{
    private $wechat;
    public function __construct()
    {
        parent::__construct();

        $options['token'] = 'wechat';
        $options['appid'] = 'wxdcc677f0fab756ed';
        $options['appsecret'] = '28e0f7453abe4872ea0a191f57795224';
        $this->wechat = new TPWechat($options);
    }

    public function index()
    {
        //dump($this->wechat->getCache('w_r'));exit;
        $this->wechat->receive();
        //$this->reply();
    }

    private function reply()
    {
        switch($this->wechat->getMsgType())
        {
            case 'event':
                $this->replyEvent();
        }
    }


    private function replyEvent()
    {
        switch($this->wechat->getEvent())
        {
            case 'subscribe':
                $this->subscribe();
        }
    }

    private function subscribe()
    {
        $xml = '<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>';
        return sprintf($xml,$this->wechat->getFromUser(),$this->wechat->getToUser(),time(),'欢迎光临这里哦！！！');
    }

}