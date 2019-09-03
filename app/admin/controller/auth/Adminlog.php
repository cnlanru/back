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
use app\admin\model\AdminLog as AdminLogModel;

class Adminlog extends Backend
{

    protected $model = null;
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];

    public function initialize()
    {
        parent::initialize();
        $this->model = new AdminLogModel();
        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds($this->auth->isSuperAdmin() ? true : false);

        $groupName = AuthGroup::where('id', 'in', $this->childrenGroupIds)
            ->column('id,name');

        $this->assign('groupdata', $groupName);
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where('admin_id', 'in', $this->childrenAdminIds)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('admin_id', 'in', $this->childrenAdminIds)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->fetch();
    }


    /**
     * 详情
     */
    public function detail($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error('未找到结果');
        $this->assign("row", $row->toArray());
        return $this->fetch();
    }

    /**
     * 添加
     * @internal
     */
    public function add()
    {
        $this->error();
    }

    /**
     * 编辑
     * @internal
     */
    public function edit($ids = NULL)
    {
        $this->error();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids)
        {
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->model->where('id', 'in', $ids)->where('admin_id', 'in', function($query) use($childrenGroupIds) {
                $query->name('auth_group_access')->field('uid');
            })->select();
            if ($adminList)
            {
                $deleteIds = [];
                foreach ($adminList as $k => $v)
                {
                    $deleteIds[] = $v->id;
                }
                if ($deleteIds)
                {
                    $this->model->destroy($deleteIds);
                    $this->success();
                }
            }
        }
        $this->error();
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        // 管理员禁止批量操作
        $this->error();
    }

    public function selectpage()
    {
        return parent::selectpage();
    }

}