<?php
/**
 * Event-事件处理
 * Created by PhpStorm.
 * User: sinre
 * Date: 2016/11/7
 * Time: 14:32
 */

namespace app\wechat\controller;

use app\wechat\model\WxUser;

class Event extends Index
{
    public function __construct()
    {
        parent::__construct();
    }

    public function inlet()
    {
        switch( $this->receive['Event'])
        {
            case 'subscribe':
                $this->subscribe();
                exit;
                break;
            case 'unsubscribe':
                $this->unsubscribe();
                exit;
                break;
            default:
                exit;
        }
    }
    /**
     * 关注公众号事件处理
     */
    private function subscribe()
    {
        $this->save_user();
        $content = "欢迎你".$this->user['nickname'];
        $this->wechat->text($content)->reply();
    }

    /**
     * 通过事件获取的用户信息
     */
    private function save_user()
    {
        $this->user['nickname_base64'] = base64_encode($this->user['nickname']);
        $this->user['tagid_list'] = $this->user['tagid_list'] ? implode(',',$this->user['tagid_list']) : '';
        if($result = WxUser::create($this->user)){
            session('user',$result);
            return true;
        }else{
            return false;
        }
    }

    /**
     * 取消关注
     */
    private function unsubscribe()
    {
        $wx_user = new WxUser;
        $wx_user->where(['openid'=>$this->receive['FromUserName']])->delete();
        session('user','');
    }
}