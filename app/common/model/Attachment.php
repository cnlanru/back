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


class Attachment extends Dbase
{

    public function setUploadtimeAttr($value)
    {
        return is_numeric($value) ? $value : strtotime($value);
    }

    protected static function init()
    {
        // 如果已经上传该资源，则不再记录
        self::beforeInsert(function ($model) {
            if (self::where('url', '=', $model['url'])->find()) {
                return false;
            }
        });
    }
}