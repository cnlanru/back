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
use app\common\model\StudyCard;
use think\Exception;

class Agent extends Api
{
    protected $noNeedLogin = "";
    protected $noNeedRight = "*";

    protected $user = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->user = $this->auth->getUser();
    }

    public function index()
    {

        $data = \app\common\model\Agent::field('id,title,price,description,tips,thumbnail')->where("status", 1)->order('weight', 'asc')->select();
        foreach ($data as &$item) {
            $item['description'] = str_replace(["\n", "\r"], ["\n", ""], $item['description']);
            if ($item['id'] == $this->user['card_id']) $currCard = $item['title'];
        }

        return $this->success('', $data);
    }

    public function update() {
        $agent_id = $this->request->request("agent_id", 0, 'intval');

        try {
            $data = \app\common\model\Agent::find($agent_id);
            if (!$data || $data['status'] != 1) {
                throw new Exception('代理等级不存在!');
            }

            \app\common\model\User::update(['agent_id' => $agent_id], ['id' => $this->user['id']]);
        } catch (Exception $ex) {
            return $this->error($ex->getMessage());
        }

        return $this->success('升级成功');

    }

}