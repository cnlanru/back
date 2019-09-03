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
use app\common\model\Article;
use app\common\model\Column;
use lanru\Date;
use lanru\Fun;

class News extends Api
{
    protected $noNeedLogin = "*";

    public function index()
    {
        $data = Column::field("id, name")->where('pid', 0)->order('weight', 'desc')->select();

        return $this->success('', $data);
    }

    public function page() {
        $pid = $this->request->param('pid', 0, 'intval');
        $page = $this->request->param('page', 1, 'intval');
        $limit = $this->request->param('limit', 10, 'intval');
        $keyword = $this->request->param('keyword', '');

        $data = new Article();

        if ($pid) {
            $data = $data->where('pid', $pid);
        }
        if (!empty($keyword)) {
            $data = $data->whereLike('title', "%{$keyword}%");
        }

        $data = $data->where('status', 1)->order('weight', 'desc')->paginate($limit);

        $res = [];

        foreach ($data->items() as $item) {
            $res[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'thumbnail' => $item['thumbnail'],
                'video' => $item['video'],
                'other' => $item['author'] . "  0评论  " . Date::human(strtotime($item['updatetime'])),
            ];
        }

        return $this->success('', $res);
    }

    public function show() {
        $id = $this->request->param('id', 0, 'intval');

        $data = Article::find($id);
        if (!$data) {
            return $this->error('内容不存在!');
        }


        if ($data['status'] == 0) {
            return $this->error('内容已隐藏!');
        }

        $data->hits = $data->hits + 1;
        $data->save();

        return $this->success('', $data);
    }

    public function dig()
    {
        $id = $this->request->param('id', 0, 'intval');

        $data = Article::find($id);
        if (!$data) {
            return $this->error('内容不存在!');
        }

        if ($data['status'] == 0) {
            return $this->error('内容已隐藏!');
        }
        $data->dig = $data->dig + 1;
        $data->save();

        return $this->success('', $data->dig);
    }
}