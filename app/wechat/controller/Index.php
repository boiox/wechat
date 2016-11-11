<?php

/**
 * Created by PhpStorm.
 * User: coolong
 * Date: 2016/10/25
 * Time: 10:40
 */
namespace app\wechat\controller;

use org\wechat_sdk\TPWechat;

class Index
{
    protected $wechat;
    protected $receive;
    protected $type;
    protected $options;
    protected $user;

    public function __construct()
    {
        $options['token'] = 'wechat';
        $options['appid'] = 'wxdcc677f0fab756ed';
        $options['appsecret'] = '28e0f7453abe4872ea0a191f57795224';
        $options['debug'] = true;
        $this->wechat = new TPWechat($options);
        $this->receive = $this->wechat->getRev()->getRevData();
        $this->user = $this->wechat->getUserInfo($this->receive['FromUserName']);
    }

    public function index()
    {
        $this->type = $this->receive['MsgType'];
        switch($this->type)
        {
            case TPWechat::MSGTYPE_EVENT:
                action('Event/inlet');
                exit;
                break;
            default:
                $this->wechat->text("暂无信息")->reply();
        }
    }

    public function log()
    {

    }

}