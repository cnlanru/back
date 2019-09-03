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
 * Date: 2019-06
 */

namespace app\admin\controller\general;


use app\common\controller\Backend;
use app\common\model\Config as ConfigModel;
use think\Exception;

class Config extends Backend
{
    /**
     * 显示
     * @return mixed
     */
    public function index()
    {
        $siteList = [];
        $groupList = ConfigModel::getGroupList();
        foreach ($groupList as $k => $v) {
            $siteList[$k]['name'] = $k;
            $siteList[$k]['title'] = $v;
            $siteList[$k]['list'] = [];
        }

        foreach (ConfigModel::all() as $k => $v) {
            if (!isset($siteList[$v['group']])) {
                continue;
            }
            $value = $v->toArray();
            $value['title'] = $value['title'];
            if (in_array($value['type'], ['select', 'selects', 'checkbox', 'radio'])) {
                $value['value'] = explode(',', $value['value']);
            }
            $value['content'] = json_decode($value['content'], TRUE);
            $value['tip'] = htmlspecialchars($value['tip']);
            $siteList[$v['group']]['list'][] = $value;
        }
        $index = 0;
        foreach ($siteList as $k => &$v) {
            $v['active'] = !$index ? true : false;
            $index++;
        }
        $this->view->assign('siteList', $siteList);
        $this->assign('groupList', $groupList)
            ->assign('typeList', ConfigModel::getTypeList());
        return $this->fetch();
    }

    /**
     * 更新
     * @param null $ids
     */
    public function edit($ids = NULL)
    {
        if ($this->request->isPost()) {
            $row = $this->request->post("row/a");
            if ($row) {
                $configList = [];
                foreach (ConfigModel::all() as $v) {
                    if (isset($row[$v['name']])) {
                        $value = $row[$v['name']];
                        if (is_array($value) && isset($value['field'])) {
                            $value = json_encode(ConfigModel::getArrayData($value), JSON_UNESCAPED_UNICODE);
                        } else {
                            $value = is_array($value) ? implode(',', $value) : $value;
                        }
                        $v['value'] = $value;
                        $configList[] = $v->toArray();
                    }
                }

                (new ConfigModel())->allowField(true)->saveAll($configList);
                try {
                    $this->refreshFile();
                } catch (Exception $e) {
                    return $this->error($e->getMessage());
                }
                return $this->success('更新成功');
            }

            return $this->error('参数不得为空!');
        }
    }

    /**
     * 添加
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param('row/a');
            if ($params) {
                foreach ($params as $k => &$v) {
                    $v = is_array($v) ? implode(',', $v) : $v;
                }
                try {
                    if (in_array($params['type'], ['select', 'selects', 'checkbox', 'radio', 'array'])) {
                        $params['content'] = json_encode(ConfigModel::decode($params['content']), JSON_UNESCAPED_UNICODE);
                    } else {
                        $params['content'] = '';
                    }
                    $result = ConfigModel::create($params);
                    if ($result !== false) {
                        try {
                            $this->refreshFile();
                        } catch (Exception $e) {
                            $this->error($e->getMessage());
                        }
                        return $this->success('新的参数已添加成功', 'index');
                    } else {
                        return $this->error($this->model->getError());
                    }
                } catch (Exception $e) {
                    return $this->error($e->getMessage());
                }
            }
        }


    }
    
    /**
     * 验证
     * @return boolean
     */
    public function check()
    {
        $params = $this->request->param('row/a');
        if ($params) {
            $config = ConfigModel::where($params)->find();

            if (!$config) {
                return $this->success();
            }

            return $this->error('名称已存在!');
        }

        return $this->error('未知参数');
    }

    /**
     * 刷新配置文件
     */
    protected function refreshFile()
    {
        $config = [];
        foreach (ConfigModel::all() as $k => $v) {

            $value = $v->toArray();
            if (in_array($value['type'], ['selects', 'checkbox', 'images', 'files'])) {
                $value['value'] = explode(',', $value['value']);
            }
            if ($value['type'] == 'array') {
                $value['value'] = (array)json_decode($value['value'], TRUE);
            }
            $config[$value['name']] = $value['value'];
        }
        file_put_contents(LANRU . 'config' . DS . 'site.php', '<?php' . "\n\nreturn " . var_export($config, true) . ";");
    }

    /**
     * 删除
     * @param string $ids
     */
    public function del($ids = "")
    {
        $name = $this->request->request('name');
        $config = ConfigModel::getByName($name);

        if ($config) {
            try {
                $config->delete();
                $this->refreshFile();
            } catch (Exception $e) {
                return $this->error($e->getMessage());
            }
            return $this->success($config['title'] . '已被删除');
        } else {
            return $this->error('参数错误');
        }
    }
}