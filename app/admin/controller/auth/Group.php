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


use app\admin\model\AuthGroup;
use app\common\controller\Backend;
use lanru\Tree;

class Group extends Backend
{
    protected $model = null;
    //当前登录管理员所有子组别
    protected $childrenGroupIds = [];
    //当前组别列表数据
    protected $groupdata = [];
    protected $noNeedRight = ['roletree'];

    public function initialize()
    {
        parent::initialize();
        $this->model = new AuthGroup();

        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);

        $groupList = collection(AuthGroup::where('id', 'in', $this->childrenGroupIds)->select())->toArray();

        Tree::instance()->init($groupList);
        $result = [];
        if ($this->auth->isSuperAdmin()) {
            $result = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
        } else {
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n) {
                $result = array_merge($result, Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['pid'])));
            }
        }
        $groupName = [];
        foreach ($result as $k => $v) {
            $groupName[$v['id']] = $v['name'];
        }

        $this->groupdata = $groupName;
        $this->view->assign('groupdata', $this->groupdata);
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $list = AuthGroup::all(array_keys($this->groupdata));
            $list = collection($list)->toArray();
            $groupList = [];
            foreach ($list as $k => $v) {
                $groupList[$v['id']] = $v;
            }
            $list = [];
            foreach ($this->groupdata as $k => $v) {
                if (isset($groupList[$k])) {
                    $groupList[$k]['name'] = $v;
                    $list[] = $groupList[$k];
                }
            }
            $total = count($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a", [], 'strip_tags');
            $params['rules'] = explode(',', $params['rules']);
            if (!in_array($params['pid'], $this->childrenGroupIds)) {
                $this->error('父组不能是其自己的子组');
            }
            $parentmodel = $this->model->find($params['pid']);
            if (!$parentmodel) {
                $this->error('找不到父组');
            }
            // 父级别的规则节点
            $parentrules = explode(',', $parentmodel->rules);
            // 当前组别的规则节点
            $currentrules = $this->auth->getRuleIds();
            $rules = $params['rules'];
            // 如果父组不是超级管理员则需要过滤规则节点,不能超过父组别的权限
            $rules = in_array('*', $parentrules) ? $rules : array_intersect($parentrules, $rules);
            // 如果当前组别不是超级管理员则需要过滤规则节点,不能超当前组别的权限
            $rules = in_array('*', $currentrules) ? $rules : array_intersect($currentrules, $rules);
            $params['rules'] = implode(',', $rules);

            if ($params) {
                $this->model->create($params);
                return $this->success();
            }
            return $this->error();
        }

        return $this->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error('未找到结果');
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a", [], 'strip_tags');
            // 父节点不能是它自身的子节点
            if (!in_array($params['pid'], $this->childrenGroupIds)) {
                $this->error('父组不能是其自己的子组');
            }
            $params['rules'] = explode(',', $params['rules']);

            $parentmodel = model("AuthGroup")->get($params['pid']);
            if (!$parentmodel) {
                $this->error('找不到父组');
            }
            // 父级别的规则节点
            $parentrules = explode(',', $parentmodel->rules);
            // 当前组别的规则节点
            $currentrules = $this->auth->getRuleIds();
            $rules = $params['rules'];
            // 如果父组不是超级管理员则需要过滤规则节点,不能超过父组别的权限
            $rules = in_array('*', $parentrules) ? $rules : array_intersect($parentrules, $rules);
            // 如果当前组别不是超级管理员则需要过滤规则节点,不能超当前组别的权限
            $rules = in_array('*', $currentrules) ? $rules : array_intersect($currentrules, $rules);
            $params['rules'] = implode(',', $rules);
            if ($params) {
                $row->save($params);
                return $this->success();
            }

            return $this->error();
        }
        $this->assign("row", $row);
        return $this->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $ids = explode(',', $ids);
            $grouplist = $this->auth->getGroups();
            $group_ids = array_map(function ($group) {
                return $group['id'];
            }, $grouplist);
            // 移除掉当前管理员所在组别
            $ids = array_diff($ids, $group_ids);

            // 循环判断每一个组别是否可删除
            $grouplist = $this->model->where('id', 'in', $ids)->select();
            $groupaccessmodel = model('AuthGroupAccess');
            foreach ($grouplist as $k => $v) {
                // 当前组别下有管理员
                $groupone = $groupaccessmodel->get(['group_id' => $v['id']]);
                if ($groupone) {
                    $ids = array_diff($ids, [$v['id']]);
                    continue;
                }
                // 当前组别下有子组别
                $groupone = $this->model->get(['pid' => $v['id']]);
                if ($groupone) {
                    $ids = array_diff($ids, [$v['id']]);
                    continue;
                }
            }
            if (!$ids) {
                return $this->error('不能删除包含子组和管理员的组');
            }
            $count = $this->model->where('id', 'in', $ids)->delete();
            if ($count) {
                return $this->success();
            }
        }
        return $this->error();
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        // 组别禁止批量操作
        return $this->error();
    }

    /**
     * 读取角色权限树
     *
     * @internal
     */
    public function roletree()
    {

        $model = $this->model;
        $id = $this->request->post("id");
        $pid = $this->request->post("pid");
        $parentGroupModel = $model->get($pid);
        $currentGroupModel = null;
        if ($id) {
            $currentGroupModel = $model->find($id);
        }
        if (($pid || $parentGroupModel) && (!$id || $currentGroupModel)) {
            $id = $id ? $id : null;
            $ruleList = collection(model('AuthRule')->order('weight', 'desc')->order('id', 'asc')->select())->toArray();
            //读取父类角色所有节点列表
            $parentRuleList = [];
            if (in_array('*', explode(',', $parentGroupModel->rules))) {
                $parentRuleList = $ruleList;
            } else {
                $parentRuleIds = explode(',', $parentGroupModel->rules);
                foreach ($ruleList as $k => $v) {
                    if (in_array($v['id'], $parentRuleIds)) {
                        $parentRuleList[] = $v;
                    }
                }
            }

            $ruleTree = new Tree();
            $groupTree = new Tree();
            //当前所有正常规则列表
            $ruleTree->init($parentRuleList);
            //角色组列表
            $groupTree->init(collection(model('AuthGroup')->where('id', 'in', $this->childrenGroupIds)->select())->toArray());

            //读取当前角色下规则ID集合
            $adminRuleIds = $this->auth->getRuleIds();
            //是否是超级管理员
            $superadmin = $this->auth->isSuperAdmin();
            //当前拥有的规则ID集合
            $currentRuleIds = $id ? explode(',', $currentGroupModel->rules) : [];

            if (!$id || !in_array($pid, $this->childrenGroupIds) || !in_array($pid, $groupTree->getChildrenIds($id, true))) {
                $parentRuleList = $ruleTree->getTreeList($ruleTree->getTreeArray(0), 'name');
                $hasChildrens = [];
                foreach ($parentRuleList as $k => $v) {
                    if ($v['haschild']) {
                        $hasChildrens[] = $v['id'];
                    }
                }
                $parentRuleIds = array_map(function ($item) {
                    return $item['id'];
                }, $parentRuleList);
                $nodeList = [];
                foreach ($parentRuleList as $k => $v) {
                    if (!$superadmin && !in_array($v['id'], $adminRuleIds)) {
                        continue;
                    }
                    if ($v['pid'] && !in_array($v['pid'], $parentRuleIds)) {
                        continue;
                    }
                    $state = array('selected' => in_array($v['id'], $currentRuleIds) && !in_array($v['id'], $hasChildrens));
                    $nodeList[] = array('id' => $v['id'], 'parent' => $v['pid'] ? $v['pid'] : '#', 'text' => $v['title'], 'type' => 'menu', 'state' => $state);
                }
                $this->success('', null, $nodeList);
            } else {
                $this->error('无法将父级更改为子级');
            }
        } else {
            $this->error('找不到组');
        }
    }
}