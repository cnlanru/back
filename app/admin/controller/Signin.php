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

namespace app\admin\controller;


use app\common\controller\Backend;

/**
 * 签到管理
 *
 * @icon fa fa-circle-o
 */
class Signin extends Backend
{
    /**
     * Signin模型对象
     * @var \app\admin\model\Signin
     */
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Signin();
    }

    /**
     * 查看
     */
    public function index()
    {
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $data = $this->model->with("user")->where($where)->order($sort, $order)
                    ->paginate($limit);

            foreach ($data->items() as $key => &$value) {
                $value->user->visible(['id', 'nickname']);
            }
            $list = collection($list = $data->items())->toArray();
            $result = array("total" => $data->count(), "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
}