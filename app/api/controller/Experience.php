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
use app\common\model\UserClasses;

//体验课
class Experience extends Api
{

    protected $noNeedLogin = '';
    protected $noNeedRight = ['my'];

    public function my () {
        $user = $this->auth->getUser();

        $limit = $this->request->param('limit', 10, 'intval');

        $data = UserClasses::with('experience')->where('user_id', $user['id'])->limit($limit)->select();

        $res = [];

        foreach ($data as $item) {
            $res[] = [
                'id' => $item['experience']['id'],
                'title' => $item['experience']['title'],
                'thumbnail' => $item['experience']['thumbnail'],
                'number' => $item['experience']['number'],
                'hits' => $item['experience']['hits'],
                'shorttitle' => $item['experience']['shorttitle'],
            ];
        }

        return $this->success('', $res);

    }
}