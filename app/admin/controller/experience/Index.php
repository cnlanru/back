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

namespace app\admin\controller\experience;


use app\common\controller\Backend;
use app\common\model\Experience;
use app\common\model\ExperienceCategory;

class Index extends Backend
{
    protected $model = null;
    protected $column = [];

    public function initialize()
    {
        parent::initialize();
        $this->model = new Experience();

        $this->column = ExperienceCategory::field('id, name')->select();

        $columnData = [0 => ['type' => 'all', 'name' => '无']];
        foreach ($this->column as $k => $v) {
            $columnData[$v['id']] = $v;
        }

        $this->assign('column', $columnData);
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