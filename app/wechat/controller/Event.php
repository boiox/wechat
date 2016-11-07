<?php
/**
 * Event-事件处理
 * Created by PhpStorm.
 * User: sinre
 * Date: 2016/11/7
 * Time: 14:32
 */

namespace app\wechat\controller;

class Event extends Index
{
    private $event;
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->event = $this->receive['Event'];
        switch($this->event)
        {
            case 'subscribe':
                $this->subscribe();
        }
    }
    /**
     * 关注公众号事件处理
     */
    public function subscribe()
    {
        $this->wechat->text('欢迎来到这里哦！')->reply();
    }
}