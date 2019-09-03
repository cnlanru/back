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


use app\common\controller\Api;
use app\common\model\Agent;
use app\common\model\Category;
use app\common\model\StudyCard;
use lanru\Fun;
use lanru\Random;
use think\Exception;
use think\facade\Config;
use think\facade\Validate;
use app\common\library\Sms;

class User extends Api
{
    protected $noNeedLogin = ['login', 'mobilelogin', 'register', 'resetpwd', 'changeemail', 'changemobile', 'third', 'logout'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
     *
     * @param string $account  账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->request('account');
        $password = $this->request->request('password');
        if (!$account || !$password) {
            $this->error('无效参数');
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $user = $this->auth->getUser();
            $data = [
                'token' => $this->auth->getToken(),
                'email' => $user['email'],
                'avatar' => Fun::formatAtt($user['avatar']),
                'id' => $user['id'],
                'password' => $user['password'],
                'group_id' => $user['group_id'],
                'username' => $user['username'],
                'nickname' => $user['nickname'],
                'mobile' => $user['mobile'],
                'gender' => $user['gender']
            ];

            //$data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success('登录成功', $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 手机验证码登录
     *
     * @param string $mobile  手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error('无效参数');
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error('手机格式错误');
        }

        /*if (!Sms::check($mobile, $captcha, 'mobilelogin')) {
            $this->error('验证码错误');
        }*/
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user) {
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        } else {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, []);
        }
        if ($ret) {
            //Sms::flush($mobile, 'mobilelogin');
            $user = $this->auth->getUser();
            $data = [
                    'token' => $this->auth->getToken(),
                    'email' => $user['email'],
                    'avatar' => Fun::formatAtt($user['avatar']),
                    'id' => $user['id'],
                    'password' => $user['password'],
                    'group_id' => $user['group_id'],
                    'username' => $user['username'],
                    'nickname' => $user['nickname'],
                    'mobile' => $user['mobile'],
                    'gender' => $user['gender']
                ];
            $this->success('登录成功', $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     */
    public function register()
    {
        $username = $this->request->request('username');
        $password = $this->request->request('password');
        $email = $this->request->request('email', '');
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$username && !$mobile) {
            $this->error('无效参数');
        }

        if (empty($username)) {
            $username = Config::get("site.user_pre") . substr($mobile, -5) . mt_rand(99, 1000);
        }

        /*if (!Sms::check($mobile, $captcha, 'register')) {
            $this->error('验证码错误');
        }*/

        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error('手机格式错误');
        }
        $ret = $this->auth->register($username, $password, $email, $mobile, []);
        if ($ret) {
            $user = $this->auth->getUser();
            $data = [
                'token' => $this->auth->getToken(),
                'email' => $user['email'],
                'avatar' => Fun::formatAtt($user['avatar']),
                'id' => $user['id'],
                'password' => $user['password'],
                'group_id' => $user['group_id'],
                'username' => $user['username'],
                'nickname' => $user['nickname'],
                'mobile' => $user['mobile'],
                'gender' => $user['gender']
            ];

            //$data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success('注册成功!', $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success('退出成功');
    }

    /**
     * 修改会员个人信息
     *
     * @param string $avatar   头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio      个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->request('username');
        $nickname = $this->request->request('nickname');
        $bio = $this->request->request('bio');
        $avatar = $this->request->request('avatar', '', 'trim,strip_tags,htmlspecialchars');
        if ($username) {
            $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error('用户名已被使用');
            }
            $user->username = $username;
        }
        $user->nickname = $nickname;
        $user->bio = $bio;
        $user->avatar = $avatar;
        $user->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @param string $email   邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->request('captcha');
        if (!$email || !$captcha) {
            $this->error('无效参数');
        }
        if (!Validate::is($email, "email")) {
            $this->error('邮箱格式错误');
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error('邮箱已被使用');
        }
        /*$result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        //Ems::flush($email, 'changeemail');*/
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @param string $email   手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error('无效参数');
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error('手机号格式错误');
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error('手机已被使用');
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error('验证码有误');
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @param string $platform 平台名称
     * @param string $code     Code码
     */
    public function third()
    {
        /*$url = url('user/index');
        $platform = $this->request->request("platform");
        $code = $this->request->request("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error('无效参数');
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo'  => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);*/
    }

    /**
     * 重置密码
     *
     * @param string $mobile      手机号
     * @param string $newpassword 新密码
     * @param string $captcha     验证码
     */
    public function resetpwd()
    {
        $type = $this->request->request("type");
        $mobile = $this->request->request("mobile");
        $email = $this->request->request("email");
        $newpassword = $this->request->request("newpassword");
        $captcha = $this->request->request("captcha");
        if (!$newpassword || !$captcha) {
            $this->error('无效参数');
        }
        if ($type == 'mobile') {
            if (!Validate::regex($mobile, "^1\d{10}$")) {
                $this->error('手机格式有误');
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user) {
                $this->error('用户不存在');
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error('验证码有误');
            }
            Sms::flush($mobile, 'resetpwd');
        } else {
            if (!Validate::is($email, "email")) {
                $this->error('邮箱格式有误');
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user) {
                $this->error('用户不存在');
            }
            /*$ret = Ems::check($email, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Ems::flush($email, 'resetpwd');*/
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success('重置密码成功');
        } else {
            $this->error($this->auth->getError());
        }
    }

    public function level()
    {
        $user = $this->auth->getUser();
        $level = $this->request->param('level', 0, 'intval');
        $iid = 0;

        try {
            $row = Category::find($level);
            if (!$row) {
                throw new Exception('类型不存在');
            }

            \app\common\model\User::update(['level' => $level], ['id' => $user['id']]);

        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }

        return $this->success('', $iid);

    }

    public function value() {
        $user = $this->auth->getUser();
        $key = $this->request->param("key", "id");

        if ($key == 'agent_group') {
            $res = [
                'agent' => 0,
                'group' => 0,
                'group_name' => '体验用户',
                'agent_name' => '我要代理',

            ];
            if ($user['card_id'] >= intval(trim(Config::get("site.max_group")))) {
                $res['group'] = 1;
            }

            if ($user['agent_id']  >= intval(trim(Config::get("site.max_agent")))) {
                $res['group'] = 1;
            }

            if ($user['card_id'] >0 && $groupName = StudyCard::where('id', $user['card_id'])->value('title')) {
                $res['group_name'] = $groupName;
            }

            if ($user['agent_id'] >0 && $agentName = Agent::where('id', $user['agent_id'])->value('title')) {
                $res['agent_name'] = $agentName;
            }

            return $this->success('', $res);

        } elseif ($key == "update") {
            $level = "";
            if ($user['level']) {
                $category = Category::find($user['level']);
                if ($category) {
                    $level = "{$category['name']}";
                    $slevel = Category::find($category['id']);
                    if ($slevel) {
                        $level .= "-{$slevel['name']}";
                    }
                }
            }

            return $this->success('', [
                    "recommend_mobile" => !empty($user['recommend_mobile']) ? $user['recommend_mobile']: '',
                    "birthday" => !empty($user['birthday']) ? $user['birthday'] : '',
                    'level' => $level
                ]);
        } else {
            if (isset($user[$key]) && !in_array($key, ['password', 'salt'])) {
                return $this->success('', $user[$key]);
            }
        }

        return $this->error('');
    }

    public function update() {
        $user = $this->auth->getUser();
        $birthday = $this->request->param("birthday");
        $choosing_grade = $this->request->param("choosing_grade", "-");
        $mobile = $this->request->param("mobile");
        list($class, $grade) = explode("-", $choosing_grade);

        try {
            $classId = 0;
            $gradeId = 0;

            if (!empty($class)) {
                $classId = Category::where("name", $class)->value("id");
                if ($classId) {
                    $gradeId = Category::where("pid", $classId)->where("name", $grade)->value("id");
                }
            }


            \app\common\model\User::update(['recommend_mobile' => $mobile, 'level' => $gradeId ? $gradeId : $classId, 'birthday' => $birthday], ['id' => $user['id']]);
        } catch (Exception $ex) {
            return $this->error($ex->getMessage());
        }

        return $this->success('更新成功');
    }
}