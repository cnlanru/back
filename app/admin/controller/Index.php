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
 * Date: 2019-06
 */

namespace app\admin\controller;


use app\admin\model\AdminLog;
use app\common\controller\Backend;
use app\common\model\User;
use think\facade\Config;
use think\Validate;
use think\facade\Hook;

class index extends Backend
{
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];

    public function index()
    {
        $userCount = User::whereBetweenTime('createtime', date('Y-m-d'))->count();

        $this->assign('userCount', $userCount);
        return $this->fetch();
    }

    /**
     * 管理员登录
     */
    public function login()
    {
        $url = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin()) {
            $this->success('您已登录，请勿再次登录', $url);
        }
        if ($this->request->isPost()) {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $keeplogin = $this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                '__token__' => 'token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                '__token__' => $token,
            ];
            if (Config::get('site.login_captcha')) {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [], ['username' => '用户名', 'password' => '密码', 'captcha' => '验证码']);

            $result = $validate->check($data);
            if (!$result) {
                return $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }
            AdminLog::setTitle('登录');
            $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);
            if ($result === true) {
                Hook::listen("admin_login_after", $this->request);
                $this->success('登录成功', $url, ['url' => $url, 'id' => $this->auth->id, 'username' => $username]);
            } else {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : '用户名或密码不正确';
                $this->error($msg, $url, ['token' => $this->request->token()]);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            $this->redirect($url);
        }

        $this->view->assign('title', '登录');
        Hook::listen("admin_login_init", $this->request);
        return $this->view->fetch();
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        Hook::listen("admin_logout_after", $this->request);
        $this->success('成功退出', 'index/login');
    }
}