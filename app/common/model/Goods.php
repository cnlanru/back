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

namespace app\common\model;



class Goods extends Dbase
{
    protected static function init()
    {
        self::afterInsert(function ($row) {
            $row->save(['weight' => $row['id']]);
        });
    }

    public function category()
    {
        return $this->belongsTo("GoodCategory", "cid", "id", [], "Left")->setEagerlyType(0);
    }
}