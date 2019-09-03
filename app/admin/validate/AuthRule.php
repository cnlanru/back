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

class AuthRule extends Validate
{
    protected $regex = ['format' => '[a-z0-9_\/]+'];

    /**
     * 验证规则
     */
    protected $rule = [
        'name'  => 'require|format|unique:AuthRule',
        'title' => 'require',
    ];

    /**
     * 提示消息
     */
    protected $message = [
        'name.format' => 'URL规则只能是小写字母、数字、下划线和/组成'
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['name','title'],
        'edit' => ['name','title']
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        $this->field = [
            'name'  => '规则',
            'title' => '标题',
        ];

        parent::__construct($rules, $message, $field);
    }
}