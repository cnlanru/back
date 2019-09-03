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

namespace app\admin\controller\user;


use app\common\controller\Backend;
use app\common\model\UserGroup;
use app\common\model\UserRule;

class Group extends Backend
{
    /**
     * @var \app\admin\model\UserGroup
     */
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $this->model = new UserGroup();
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function add()
    {
        $nodeList = UserRule::getTreeList();
        $this->assign("nodeList", $nodeList);
        return parent::add();
    }

    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            return $this->error('未找到结果');
        $rules = explode(',', $row['rules']);
        $nodeList = UserRule::getTreeList($rules);
        $this->assign("nodeList", $nodeList);
        return parent::edit($ids);
    }
}