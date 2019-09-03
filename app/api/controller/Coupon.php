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

class Coupon extends Api
{
    protected $noNeedLogin = "";
    protected $noNeedRight = "*";

    protected $user = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->user = $this->auth->getUser();
    }

    public function index()
    {
        return $this->success('', []);
    }

    public function exchange()
    {
        return $this->error('兑换失败!此码不存在');
    }
}