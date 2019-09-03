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
 * Date: 2019-07
 */

namespace app\admin\validate;


use think\Validate;

class Admin extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'username' => 'require|max:50|unique:admin',
        'password' => 'require'
    ];

    /**
     * 提示消息
     */
    protected $message = [
    ];

    /**
     * 字段描述
     */
    protected $field = [
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['username', 'password'],
        'edit' => ['username'],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'username' => '用户名',
            'password' => '密码'
        ];
        parent::__construct($rules, $message, $field);
    }
}