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
 * Date: 2019-07
 */

namespace app\admin\controller\auth;


use app\admin\model\AuthRule;
use app\common\controller\Backend;
use lanru\Fun;
use lanru\Tree;

class Rule extends Backend
{
    protected $model = null;
    protected $multiFields = 'ismenu,status';
    protected $noNeedLogin = ['index'];

    public function initialize()
    {
        parent::initialize();
        $this->model = new AuthRule();

        $ruleList = collection($this->model->order('weight', 'desc')->order('id', 'asc')->select())->toArray();
        Tree::instance()->init($ruleList);
        $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
        $ruledata = [0 => '无'];
        foreach ($this->rulelist as $k => &$v) {
            if (!$v['ismenu']) {
                continue;
            }
            $ruledata[$v['id']] = $v['title'];
        }
        unset($v);
        $this->view->assign('ruledata', $ruledata);
    }
    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $list = $this->rulelist;

            return json(['total' => count($this->rulelist), 'rows' => $list]);
        }

        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params) {
                if (!$params['ismenu'] && !$params['pid']) {
                    return $this->error('非菜单规则节点必须有父级');
                }

                $result = $this->validate($params, Fun::getValidateClass($this->model));
                if ($result !== true) {
                    return $this->error($result);
                }

                if ($this->model->save($params)) {
                    return $this->success('添加成功!', url('index'));
                }

            }
            return $this->error('参数错误');

        }

        return $this->view->fetch();

    }

    /**
     * 编辑
     */
    public function edit($ids = 0)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            return $this->error('未找到结果');
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params) {
                if (!$params['ismenu'] && !$params['pid']) {
                    return $this->error('非菜单规则节点必须有父级');
                }
                if ($params['pid'] != $row['pid']) {
                    $childrenIds = Tree::instance()->init(collection(AuthRule::select())->toArray())->getChildrenIds($row['id']);
                    if (in_array($params['pid'], $childrenIds)) {
                        return $this->error('无法将父级更改为子级');
                    }
                }
                //这里需要针对name做唯一验证
                $result = $this->validate($params, Fun::getValidateClass($this->model, 'edit'));
                if ($result !== true) {
                    return $this->error($result);
                }

                $result = $row->save($params);
                if ($result === false) {
                    return $this->error($row->getError());
                }

                return $this->success();
            }
            return $this->error();
        }

        $this->view->assign("row", $row);
        return $this->view->fetch();
        
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $delIds = [];
            foreach (explode(',', $ids) as $k => $v) {
                $delIds = array_merge($delIds, Tree::instance()->getChildrenIds($v, true));
            }
            $delIds = array_unique($delIds);
            $count = $this->model->where('id', 'in', $delIds)->delete();
            if ($count) {
                return $this->success();
            }
        }
        return $this->error();
    }
}