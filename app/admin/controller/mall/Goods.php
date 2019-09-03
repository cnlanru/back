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

namespace app\admin\controller\mall;


use app\common\controller\Backend;
use app\common\model\GoodCategory;
use lanru\Tree;

class Goods extends Backend
{
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Goods();

        // 必须将结果集转换为数组
        $ruleList = collection(GoodCategory::order('weight', 'desc')->order('id', 'asc')->select())->toArray();

        Tree::instance()->init($ruleList);
        $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
        $parentList = [0 => '无'];
        foreach ($this->rulelist as $k => &$v) {
            $parentList[$v['id']] = $v['name'];
        }
        unset($v);
        $this->view->assign('parentList', $parentList);
    }

    public function index()
    {
        if ($this->request->isAjax()) {
            list($where, $order, $sort, $offset, $limit) = $this->buildparams();
            $data = $this->model->with('category')->where($where)
                ->order($order, $sort)->paginate($limit);

            return json(['total' => $data->total(), 'rows' => $data->items()]);
        }
        return $this->fetch();
    }

}