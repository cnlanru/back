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

namespace app\common\model;

class Config extends Dbase
{
    protected $createTime = false;
    protected $updateTime = false;

    /**
     * 将字符串解析成键值数组
     * @param string $text
     * @return array
     */
    public static function decode($text, $split = "\r\n")
    {
        $content = explode($split, $text);
        $arr = [];
        foreach ($content as $k => $v) {
            if (stripos($v, "|") !== false) {
                $item = explode('|', $v);
                $arr[$item[0]] = $item[1];
            }
        }
        return $arr;
    }

    /*
     * 读取分类分组列表
     * @return array
     */
    public static function getGroupList()
    {
        $groupList = config('site.configgroup');
        return $groupList;
    }

    /**
     * 读取配置类型
     * @return array
     */
    public static function getTypeList()
    {
        $typeList = [
            'string'   => '文字',
            'text'     => '多行文字',
            'number'   => '数字',
            'date'     => '日期',
            'time'     => '时间',
            'datetime' => '日期时间',
            'select'   => '列表',
            'selects'  => '列表(多选)',
            'image'    => '图片',
            'images'   => '图片(多)',
            'file'     => '文件',
            'files'    => '文件(多)',
            'checkbox' => '复选',
            'radio'    => '单选',
            'array'    => '数组   ',
        ];
        return $typeList;
    }

}