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
use app\common\model\Courseware;
use app\common\model\CoursewareCategory;
use think\Db;

class School extends Api
{
    protected $noNeedLogin = '*';

    public function index()
    {
        $cid = $this->request->param('cid', 0, 'intval');
        $limit = $this->request->param('limit', 10, 'intval');
        $flag = $this->request->param('flag', 0, 'intval');

        $data = Courseware::field('title, id, thumbnail, hits, number')->where('cid', $cid);
        $data = $data->where('flag', $flag);
        $data = $data->where(Db::raw("thumbnail <> ''"));
        $data = $data->where('status', 1)->order('weight', 'desc')
            ->limit($limit)->select();

        return $this->success('', $data);
    }

    public function search()
    {
        $cid = $this->request->param('cid', 0, 'intval');
        $limit = $this->request->param('limit', 10, 'intval');
        $flag = $this->request->param('flag', 0, 'intval');

        $data = Courseware::field('title, id, thumbnail, hits, number')->where('cid', $cid);
        $data = $data->where('flag', $flag);
        $data = $data->where(Db::raw("thumbnail <> ''"));
        $data = $data->where('status', 1)->order('weight', 'desc')
                ->paignate();

        return $this->success('', $data);
    }

    public function category()
    {
        $pid = $this->request->param('pid', 2, 'intval');
        $limit = $this->request->param('limit', 4, 'intval');

        $data = CoursewareCategory::find($pid);
        if (!$data) {
            return $this->error('分类不存在');
        }

        $sonData = CoursewareCategory::where('pid', $pid)->where(Db::raw("image != ''"))->limit($limit)->select();

        $rsc = [];

        foreach ($sonData as $item) {
            $rsc[] = [
                'id' => $item['id'],
                'name' => sprintf("%s_%s", $data['name'], $item['name']),
                'thumbnail' => $item['image']
            ];
        }

        return $this->success('', $rsc);


    }

    public function categoryInfo() {
        $id = $this->request->param('id', 0, 'intval');

        $data = CoursewareCategory::find($id);
        if (!$data) {
            return $this->error('分类不存在');
        }

        return $this->success('', [
            'id' => $data['id'],
            'name' => $data['name'],
            'image' => $data['image'],
        ]);
    }

}