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
use app\common\model\GoodCategory;
use app\common\model\Goods;
use think\Db;

class Good extends Api
{
    protected $noNeedRight = '*';
    protected $noNeedLogin = ['category', 'index'];


    public function index() {
        $page = $this->request->param('page', 1, 'intval');
        $pagesize = $this->request->param('pagesize', 10, 'intval');
        $isCommend = $this->request->param('iscommend', 0, 'intval');
        $cid = $this->request->param('cid', 0, 'intval');
        $keyword = $this->request->param('keyword', '');

        $data = Goods::field('updatetime,body', true);
        if ($cid) $data = $data->where('cid', $cid);
        if ($isCommend) $data = $data->where('flag', $isCommend);
        if (!empty($keyword)) $data = $data->whereLike('title', "%{$keyword}%");
        $data = $data->where('status', 1)->page($page)->limit($pagesize)->select();

        foreach ($data as &$item) {
            if (!empty($item['album'])) {
                $item = explode(",", $item['album']);
            }
        }

        return $this->success('', $data);
    }

    public function detail () {
        $id = $this->request->param('id', 0, 'intval');
        $data = Goods::find($id);
        if (!$data) {
            return $this->error('商品不存在!');
        }

        if ($data['status'] == 0) {
            return $this->error('商品已下架!');
        }

        if (!empty($data['album'])) {
            $data['album'] = explode(",", $data['album']);
        } else {
            $data['album'] = [];
        }

        return $this->success('', $data);
    }

    public function category() {
        $pageSize = $this->request->param("pagesize", 1000, 'intval');
        $isImage = $this->request->param("image", 0, 'intval');
        $data = GoodCategory::field('name, id, image');
        if ($isImage) {
            $data = $data->where(Db::raw("image != ''"));
        }
        $data = $data->order("weight desc")->limit($pageSize)->select();

        return $this->success('', $data);
    }

}