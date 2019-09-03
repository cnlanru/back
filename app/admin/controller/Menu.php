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
use app\common\library\Menu as MenuLib;
use app\common\model\Ad as Admodel;

class Menu extends Backend
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];
    protected $model = null;

    public function initialize()
    {
        parent::initialize();

        $this->model = new Admodel();
    }

    public function index()
    {

        $menu = [
            [
                'name'    => 'signin',
                'title'   => '签到管理',
                'icon'    => 'fa fa-map-marker',

                'sublist' => [
                    ['name' => 'signin/index', 'title' => '查看'],
                    ['name' => 'signin/add', 'title' => '添加'],
                    ['name' => 'signin/edit', 'title' => '编辑'],
                    ['name' => 'signin/multi', 'title' => '批量'],
                    ['name' => 'signin/del', 'title' => '删除']
                    /*[
                        'name'    => 'other/agent',
                        'title'   => '代理管理',
                        'icon'    => 'fa fa-list-ul',
                        'sublist' => [
                            ['name' => 'other/agent/index', 'title' => '查看'],
                            ['name' => 'other/agent/add', 'title' => '添加'],
                            ['name' => 'other/agent/edit', 'title' => '编辑'],
                            ['name' => 'other/agent/multi', 'title' => '批量'],
                            ['name' => 'other/agent/del', 'title' => '删除']
                        ],
                        'ismenu' => 1,
                        'status' => 1
                    ],
                    [
                        'name'    => 'other/card',
                        'title'   => '学习卡管理',
                        'icon'    => 'fa fa-list-ul',
                        'sublist' => [
                            ['name' => 'other/card/index', 'title' => '查看'],
                            ['name' => 'other/card/add', 'title' => '添加'],
                            ['name' => 'other/card/edit', 'title' => '编辑'],
                            ['name' => 'other/card/multi', 'title' => '批量'],
                            ['name' => 'other/card/del', 'title' => '删除']
                        ],
                        'ismenu' => 1,
                        'status' => 1
                    ]

                    /*,
                    [
                        'name'    => 'shop/group',
                        'title'   => '团购商品管理',
                        'icon'    => 'fa fa-list-ul',
                        'sublist' => [
                            ['name' => 'shop/group/add', 'title' => '添加'],
                            ['name' => 'shop/group/edit', 'title' => '编辑'],
                            ['name' => 'shop/group/del', 'title' => '删除'],
                            ['name' => 'shop/group/multi', 'title' => '批量'],
                        ],
                        'ismenu' => 1,
                        'status' => 1
                    ],
                    [
                        'name'    => 'shop/coupon',
                        'title'   => '优惠券管理',
                        'icon'    => 'fa fa-list-ul',
                        'sublist' => [
                            ['name' => 'shop/coupon/add', 'title' => '添加'],
                            ['name' => 'shop/coupon/edit', 'title' => '编辑'],
                            ['name' => 'shop/coupon/del', 'title' => '删除'],
                            ['name' => 'shop/coupon/multi', 'title' => '批量'],
                        ],
                        'ismenu' => 1,
                        'status' => 1
                    ]*/
                ],
                'ismenu' => 1,
                'status' => 1
            ]
        ];
        MenuLib::create($menu);

        echo "ok";
        //return $this->fetch();
    }

}