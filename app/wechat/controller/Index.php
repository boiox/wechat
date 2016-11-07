<?php

/**
 * Created by PhpStorm.
 * User: coolong
 * Date: 2016/10/25
 * Time: 10:40
 */
namespace app\wechat\controller;

use think\Controller;

use org\wechat_sdk\TPWechat;

class Index extends Controller
{
    private $wechat;
    private $receive;
    private $type;
    public function __construct()
    {
        parent::__construct();

        $options['token'] = 'wechat';
        $options['appid'] = 'wxdcc677f0fab756ed';
        $options['appsecret'] = '28e0f7453abe4872ea0a191f57795224';
        $options['debug'] = true;
        $this->wechat = new TPWechat($options);
    }

    public function index()
    {
        $this->receive = $this->wechat->getRev()->getRevData();

        $this->type = $this->receive['MsgType'];
        switch($this->type)
        {
            case TPWechat::MSGTYPE_EVENT:
                action('Event/index');
                exit;
                break;
            default:
                $this->wechat->text("help info")->reply();
        }
    }

}