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

namespace app\admin\controller\ad;


use app\common\controller\Backend;
use app\common\model\Ad;

class Adlist extends Backend
{
    protected $model = null;
    protected $sort = [];

    public function initialize()
    {
        parent::initialize();
        $this->model = new Ad();

        $this->sort = \app\common\model\AdSort::select();
        $sort = [];
        foreach ($this->sort as $item) {
            $sort[$item['id']] = $item['name'];
        }

        $this->assign('sort', $sort);
    }

    public function index()
    {
        if ($this->request->isAjax()) {

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $rows = $this->model->where($where)->order($sort, $order)->paginate($limit);


            return ['total' => $rows->total(), 'rows' => $rows->items()];

        }

        return $this->fetch();
    }

}