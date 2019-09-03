<?php

/**
 * LRCODE
 * ============================================================================
 * 版权所有 2016-2030 江苏蓝儒网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.lanru.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: 潇声
 * Date: 2019-08
 */

namespace app\api\controller;

use app\common\model\User;
use think\facade\Validate;
use app\common\controller\Api;
use app\common\model\Sms as SmsModel;
use app\common\library\Sms as Smslib;

class Sms extends Api
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    /**
     * 发送验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     */
    public function send()
    {
        $mobile = $this->request->request("mobile");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';

        if (!$mobile || !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error('手机号不正确');
        }
        $last = Smslib::get($mobile, $event);
        if ($last && time() - strtotime($last['createtime']) < 60) {
            $this->error('发送频繁');
        }
        $ipSendTotal = SmsModel::where(['ip' => $this->request->ip()])->whereTime('createtime', '-1 hours')->count();
        if ($ipSendTotal >= 5) {
            $this->error('发送频繁');
        }
        if ($event) {
            $userinfo = User::getByMobile($mobile);
            if ($event == 'register' && $userinfo) {
                //已被注册
                $this->error('已被注册');
            } elseif (in_array($event, ['changemobile']) && $userinfo) {
                //被占用
                $this->error('已被占用');
            } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                //未注册
                $this->error('未注册');
            }
        }
        $ret = Smslib::send($mobile, null, $event);
        if ($ret) {
            $this->success('发送成功');
        } else {
            $this->error('发送失败');
        }
    }

    /**
     * 检测验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     * @param string $captcha 验证码
     */
    public function check()
    {
        $mobile = $this->request->request("mobile");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';
        $captcha = $this->request->request("captcha");

        if (!$mobile || !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error('手机号不正确');
        }
        if ($event) {
            $userinfo = User::getByMobile($mobile);
            if ($event == 'register' && $userinfo) {
                //已被注册
                $this->error('已被注册');
            } elseif (in_array($event, ['changemobile']) && $userinfo) {
                //被占用
                $this->error('已被占用');
            } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                //未注册
                $this->error('未注册');
            }
        }
        $ret = Smslib::check($mobile, $captcha, $event);
        if ($ret) {
            $this->success('成功');
        } else {
            $this->error('验证码不正确');
        }
    }
}