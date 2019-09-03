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

namespace app\admin\controller;

use app\common\controller\Backend;
use think\addons\AddonException;
use think\addons\Service;
use think\Exception;
use think\facade\App;
use think\facade\Cache;
use think\facade\Config;

class Addon extends Backend
{
    protected $model = null;

    public function index()
    {
        if ($this->request->isAjax()) {
            $addons = get_addon_list();
            $row = [];
            foreach ($addons as $k => &$v) {
                $config = get_addon_config($v['name']);
                $v['config'] = $config ? 1 : 0;
                $v['url'] = str_replace($this->request->server('SCRIPT_NAME'), '', $v['url']);
                $row[] = $v;
            }


            return json(['total' => count($row), 'rows' => $row]);
        }

        return $this->fetch();
    }

    /**
     * 禁用启用
     */
    public function state()
    {
        $name = $this->request->post("name");
        $action = $this->request->post("action");
        $force = (int)$this->request->post("force");
        if (!$name) {
            return $this->error('参数name不能为空');
        }
        try {
            $action = $action == 'enable' ? $action : 'disable';
            //调用启用、禁用的方法
            Service::$action($name, $force);
            Cache::rm('__menu__');
            return $this->success('操作成功!');
        } catch (AddonException $e) {
           return $this->result($e->getData(), $e->getCode(), $e->getMessage());
        } catch (Exception $e) {
           return $this->error($e->getMessage());
        }
    }

    /**
     * 本地上传
     */
    public function local()
    {
        Config::set('default_return_type', 'json');
        $file = $this->request->file('file');
        $addonTmpDir = App::getRuntimePath() . 'addons' . DS;
        if (!is_dir($addonTmpDir)) {
            @mkdir($addonTmpDir, 0755, true);
        }

        $info = $file->rule('uniqid')->validate(['size' => 10240000, 'ext' => 'zip'])->move($addonTmpDir);
        if ($info) {
            $tmpName = substr($info->getFilename(), 0, stripos($info->getFilename(), '.'));
            $tmpAddonDir = ADDON_PATH . $tmpName . DS;
            $tmpFile = $addonTmpDir . $info->getSaveName();

            try {
                Service::unzip($tmpName);
                @unlink($tmpFile);
                $infoFile = $tmpAddonDir . 'info.ini';
                if (!is_file($infoFile)) {
                    throw new Exception('找不到插件信息文件');
                }

                $config = Config::load($infoFile, $tmpName);
                $name = isset($config['name']) ? $config['name'] : '';
                if (!$name) {
                    throw new Exception('插件信息文件数据不正确');
                }

                $newAddonDir = ADDON_PATH . $name . DS;
                if (is_dir($newAddonDir)) {
                    throw new Exception('插件已存在 ');
                }

                //重命名插件文件夹
                rename($tmpAddonDir, $newAddonDir);
                try {
                    //默认禁用该插件
                    $info = get_addon_info($name);
                    if ($info['state']) {
                        $info['state'] = 0;
                        set_addon_info($name, $info);
                    }

                    //执行插件的安装方法
                    $class = get_addon_class($name);
                    if (class_exists($class)) {
                        $addon = new $class();
                        $addon->install();
                    }

                    //导入SQL
                    Service::importsql($name);

                    $info['config'] = get_addon_config($name) ? 1 : 0;
                    return $this->success('离线安装提示', null, ['addon' => $info]);
                } catch (Exception $e) {
                    @rmdirs($newAddonDir);
                    throw new Exception($e->getMessage());
                }
            } catch (Exception $e) {
                @unlink($tmpFile);
                @rmdirs($tmpAddonDir);
                return $this->error($e->getMessage());
            }
        } else {
            // 上传失败获取错误信息
            return $this->error($file->getError());
        }
    }

    /**
     * 安装
     */
    public function install()
    {
        $name = $this->request->post("name");
        $force = (int)$this->request->post("force");
        if (!$name) {
            return $this->error('参数name不能为空');
        }
        try {
            $uid = $this->request->post("uid");
            $token = $this->request->post("token");
            $version = $this->request->post("version");
            $faversion = $this->request->post("faversion");
            $extend = [
                'uid'       => $uid,
                'token'     => $token,
                'version'   => $version,
                'faversion' => $faversion
            ];
            Service::install($name, $force, $extend);
            $info = get_addon_info($name);
            $info['config'] = get_addon_config($name) ? 1 : 0;
            $info['state'] = 1;
            return $this->success('安装成功', null, ['addon' => $info]);
        } catch (AddonException $e) {
            return $this->result($e->getData(), $e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 卸载
     */
    public function uninstall()
    {
        $name = $this->request->post("name");
        $force = (int)$this->request->post("force");
        if (!$name) {
            return $this->error('参数name不能为空');
        }
        try {
            Service::uninstall($name, $force);
            return $this->success('成功卸载！');
        } catch (AddonException $e) {
            return $this->result($e->getData(), $e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}