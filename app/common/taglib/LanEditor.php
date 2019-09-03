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

namespace app\common\taglib;


use think\template\TagLib;

class LanEditor extends TagLib
{
    protected $tags = [
        'init' => ['attr' => 'url, name, id', 'close' => 1]
    ];

    public function tagInit($tag, $content)
    {
        $url = empty($tag['url']) ? url('ajax/editor') : url($tag['url']);
        $name = empty($tag['name']) ? 'contents' : $tag['name'];
        $id = empty($tag['id']) ? 'container' : $tag['id'];

        $parse = '<script id="'.$id.'" name="'.$name.'" type="text/plain">'.$content.'</script>';
        $parse .= '<script type="text/javascript" src="/assets/plugin/editor/ueditor.config.js"></script>';
        $parse .= '<script type="text/javascript" src="/assets/plugin/editor/ueditor.all.js"></script>';
        $parse .= '<script type="text/javascript">';
        $parse .= 'var '.$id.' = UE.getEditor("'.$id.'", {serverUrl:"'.$url.'"});';
        $parse .= '</script>';
        return $parse;

    }
}