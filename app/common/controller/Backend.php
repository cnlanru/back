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

namespace app\common\controller;

use app\admin\library\Auth;
use think\facade\Hook;
use think\facade\Session;
use think\Loader;

/**
 * @brief 后台基类
 * Class Backend
 * @package app\common\controller
 */
class Backend extends Base
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 权限控制类
     * @var Auth
     */
    protected $auth = null;

    /**
     * 快速搜索时执行查找的字段
     */
    protected $searchFields = 'id';

    /**
     * Multi方法可批量修改的字段
     */
    protected $multiFields = 'status';

    /**
     * 数据限制字段
     */
    protected $dataLimitField = 'admin_id';

    /**
     * 数据限制开启时自动填充限制字段值
     */
    protected $dataLimitFieldAutoFill = true;

    /**
     * 是否是关联查询
     */
    protected $relationSearch = false;

    /**
     * Selectpage可显示的字段
     */
    protected $selectpageFields = '*';

    /**
     * 模型对象
     * @var \think\Model
     */
    protected $model = null;

    /**
     * 是否开启Validate验证
     */
    protected $modelValidate = false;

    /**
     * 是否开启模型场景验证
     */
    protected $modelSceneValidate = false;

    /**
     * 是否开启数据限制
     * 支持auth/personal
     * 表示按权限判断/仅限个人
     * 默认为禁用,若启用请务必保证表中存在admin_id字段
     */
    protected $dataLimit = false;

    /**
     * 前台提交过来,需要排除的字段数据
     */
    protected $excludeFields = "";



    use \app\admin\library\traits\Backend;

    public function initialize()
    {
        parent::initialize();

        // 定义是否Dialog请求
        !defined('IS_DIALOG') && define('IS_DIALOG', $this->request->get("dialog") ? true : false);

        $modulename = $this->request->module();
        $controllername = Loader::parseName($this->request->controller());
        $actionname = strtolower($this->request->action());

        $path = str_replace('.', '/', $controllername) . '/' . $actionname;

        $this->auth = Auth::instance();

        // 设置当前请求的URI
        $this->auth->setRequestUri($path);

        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            //检测是否登录
            if (!$this->auth->isLogin()) {
                Hook::listen('admin_nologin', $this);
                $url = Session::get('referer');
                $url = $url ? $url : $this->request->url();
                if ($url == '/') {
                    $this->redirect('index/login', [], 302, ['referer' => $url]);
                    exit;
                }
                return $this->error('请先登录', url('index/login', ['url' => $url]));
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($path)) {
                    Hook::listen('admin_nopermission', $this);
                    //return $this->error('你没有权限', '');
                }
            }
        }

        // 设置面包屑导航数据
        $breadcrumb = $this->auth->getBreadCrumb($path);
        array_pop($breadcrumb);
        $this->view->breadcrumb = $breadcrumb;

        //渲染权限对象
        $this->assign('auth', $this->auth);
        //渲染管理员对象
        $this->assign('admin', Session::get('admin'));

        //左侧菜单
        list($menulist, $navlist, $fixedmenu, $referermenu) = $this->auth->getSidebar([], $controllername);
        $this->view->assign('menulist', $menulist);
    }

    /**
     * 获取数据限制的管理员ID
     * 禁用数据限制时返回的是null
     * @return mixed
     */
    protected function getDataLimitAdminIds()
    {

        if ($this->auth->isSuperAdmin()) {
            return null;
        }
        $adminIds = $this->auth->getChildrenAdminIds(true);
        return $adminIds;
    }


    /**
     * 渲染配置信息
     * @param mixed $name  键名或数组
     * @param mixed $value 值
     */
    protected function assignconfig($name, $value = '')
    {
        $this->assign('config', array_merge($this->view->config ? $this->view->config : [], is_array($name) ? $name : [$name => $value]));
    }
}