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
use app\common\model\Ad;
use app\common\model\Category;
use lanru\Fun;
use lanru\Tree;
use think\Exception;
use think\facade\Config;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
        $this->success('请求成功');
    }

    public function ad()
    {
        $pid = $this->request->param('pid', 1);

        try {
            $data = Ad::where('pid', $pid)->field('remark')->order('id', 'desc')->select();
            foreach ($data as &$item) {
                $item['remark'] = Fun::formatAtt($item['remark']);
            }
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }


        $this->success('', $data);
    }

    //分类 等级 年级
    public function grade()
    {
        $pid = $this->request->param('pid', 0, 'intval');

        $data = Category::field('id, name');
        $data = $data->where('pid', $pid);
        $data = $data->order('weight', 'asc')->select();
        return $this->success('', $data);
    }

    public function choosingGrade() {
        $all = Category::where('pid', 0)->order('weight asc, id asc')->select();

        $rec = [];
        foreach ($all as $item) {
            $rec[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'son' => Category::where('pid', $item['id'])->field('id, name')->order('weight asc, id asc')->select()
            ];
        }

        return $this->success('', $rec);

    }
}