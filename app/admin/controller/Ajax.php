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


use lanru\Fun;
use think\facade\App;
use think\facade\Cache;
use think\facade\Config;
use app\common\controller\Backend;
use lanru\Random;
use think\facade\Hook;

class Ajax extends Backend
{

    public function initialize()
    {
        parent::initialize();

        //设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);
    }

    /**
     * 上传文件
     */
    public function upload()
    {
        Config::set('default_return_type', 'json');
        $rec = $this->uploader();
        if ($rec['code'] == 0) {
            return $this->error($rec['error']);
        } else {
            return $this->success('上传成功', null, [
                'url' => Fun::formatAtt($rec['url'])
            ]);
        }
    }

    /**
     * 清空系统缓存
     */
    public function wipecache()
    {
        $type = $this->request->request("type");
        switch ($type) {
            case 'all':
                rmdirs(App::getRuntimePath(). 'log/', false);
            case 'content':
                rmdirs(App::getRuntimePath(). 'cache/', false);
                Cache::clear();
                if ($type == 'content')
                    break;
            case 'template':
                ;
                rmdirs(App::getRuntimePath(). 'temp/', false);
                if ($type == 'template')
                    break;
            case 'addons':
                //Server::refresh();
                if ($type == 'addons')
                    break;
        }

        Hook::listen("wipecache_after");
        return $this->success();
    }

    //编辑器
    public function editor()
    {
        $action = $this->request->get('action');
        $CONFIG = file_get_contents(LANRU . 'config' . DS . 'config.json');
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $CONFIG), true);
        $upload = Config::get('upload.');

        switch ($action) {
            case 'config':
                echo json_encode($CONFIG);
                exit();
                break;
            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $rec = $this->uploader();

                $result = json_encode(array(
                    "state" => $rec['code'] == 0 ? 'error' : 'SUCCESS',          //上传状态，上传成功时必须返回"SUCCESS"
                    "url" => Fun::formatAtt($rec['url']),            //返回的地址
                    "title" => $rec['title'],          //新文件名
                    "original" => $rec['original'],       //原始文件名
                    "type" => $rec['type'],            //文件类型
                    "size" => $rec['size'],           //文件大小
                ));
                break;
            /* 列出图片 */
            case 'listimage':
                $allowFiles = $CONFIG['imageManagerAllowFiles'];
                $dir = explode('/', trim($upload['savekey'], '/'));
                $path = LANRU . "public" . DS . $dir[0];
                $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
                $files = $this->getfiles($path, $allowFiles);

                $listSize = $CONFIG['imageManagerListSize'];
                $size = $this->request->get('size', $listSize);
                $start = $this->request->get('start', 0);
                $end = $start + $size;

                if (!count($files)) {
                    $result = json_encode(array(
                        "state" => "no match file",
                        "list" => array(),
                        "start" => $start,
                        "total" => count($files)
                    ));
                }

                $len = count($files);
                for ($i = min($end, $len) - 1, $list = array();
                     $i < $len && $i >= 0 && $i >= $start; $i--){
                    $list[] = $files[$i];
                }


                /* 返回数据 */
                $result = json_encode(array(
                    "state" => "SUCCESS",
                    "list" => $list,
                    "start" => $start,
                    "total" => count($files)
                ));

                break;
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $CONFIG['fileManagerAllowFiles'];
                $dir = explode('/', trim($upload['savekey'], '/'));
                $path = LANRU . "public" . DS . $dir[0] . DS;
                $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
                $files = $this->getfiles($path, $allowFiles);

                $listSize = $CONFIG['fileManagerListSize'];
                $size = $this->request->get('size', $listSize);
                $start = $this->request->get('start', 0);
                $end = $start + $size;

                if (!count($files)) {
                    $result = json_encode(array(
                        "state" => "no match file",
                        "list" => array(),
                        "start" => $start,
                        "total" => count($files)
                    ));
                }

                $len = count($files);
                for ($i = min($end, $len) - 1, $list = array();
                     $i < $len && $i >= 0 && $i >= $start; $i--){
                    $list[] = $files[$i];
                }


                /* 返回数据 */
                $result = json_encode(array(
                    "state" => "SUCCESS",
                    "list" => $list,
                    "start" => $start,
                    "total" => count($files)
                ));
                break;

            /* 抓取远程文件 */
            case 'catchimage':


                $fieldName = $CONFIG['catcherFieldName'];
                /* 抓取远程图片 */
                $list = array();
                if (isset($_POST[$fieldName])) {
                    $source = $_POST[$fieldName];
                } else {
                    $source = $_GET[$fieldName];
                }
                foreach ($source as $imgUrl) {
                    $item = $this->saveRemote($imgUrl, $CONFIG);
                    if ($item['code'] == 1) {
                        array_push($list, array(
                            "state" => $item["state"],
                            "url" => Fun::formatAtt($item["url"]),
                            "size" => $item["size"],
                            "title" => htmlspecialchars($item["title"]),
                            "original" => htmlspecialchars($item["original"]),
                            "source" => htmlspecialchars($imgUrl)
                        ));
                    }
                }

                /* 返回抓取数据 */
                $result = json_encode(array(
                    'state'=> count($list) ? 'SUCCESS':'ERROR',
                    'list'=> $list
                ));
                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        echo $result;
        exit();
    }

    //上传文件/图片
    protected function uploader() {
        $file = $this->request->file('file');
        if (empty($file)) {
            return ['code' => 0, 'error' => '未上传文件'];
        }

        //判断是否已经存在附件
        $sha1 = $file->hash();
        $extparam = $this->request->post();

        $upload = Config::get('upload.');

        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int)$upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
        $fileInfo = $file->getInfo();
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $mimetypeArr = explode(',', strtolower($upload['mimetype']));
        $typeArr = explode('/', $fileInfo['type']);

        //验证文件后缀
        if ($upload['mimetype'] !== '*' &&
            (
                !in_array($suffix, $mimetypeArr)
                || (stripos($typeArr[0] . '/', $upload['mimetype']) !== false && (!in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr)))
            )
        ) {
            return ['code' => 0, 'error' => '上传的文件格式有误'];
        }
        $replaceArr = [
            '{year}'     => date("Y"),
            '{mon}'      => date("m"),
            '{day}'      => date("d"),
            '{hour}'     => date("H"),
            '{min}'      => date("i"),
            '{sec}'      => date("s"),
            '{random}'   => Random::alnum(16),
            '{random32}' => Random::alnum(32),
            '{filename}' => $suffix ? substr($fileInfo['name'], 0, strripos($fileInfo['name'], '.')) : $fileInfo['name'],
            '{suffix}'   => $suffix,
            '{.suffix}'  => $suffix ? '.' . $suffix : '',
            '{filemd5}'  => md5_file($fileInfo['tmp_name']),
        ];
        $savekey = $upload['savekey'];
        $savekey = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        $fileName = substr($savekey, strripos($savekey, '/') + 1);

        $splInfo = $file->validate(['size' => $size])
            ->move(LANRU . "public" . DS . $uploadDir, $fileName);
        if ($splInfo) {
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            $params = array(
                'admin_id'    => (int)$this->auth->id,
                'user_id'     => 0,
                'filesize'    => $fileInfo['size'],
                'imagewidth'  => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype'   => $suffix,
                'imageframes' => 0,
                'mimetype'    => $fileInfo['type'],
                'url'         => $uploadDir . $splInfo->getSaveName(),
                'uploadtime'  => time(),
                'storage'     => 'local',
                'sha1'        => $sha1,
                'extparam'    => json_encode($extparam),
            );
            $attachment = model("attachment");
            $attachment->data(array_filter($params));
            $attachment->save();
            Hook::listen("upload_after", $attachment);
            return ['code' => 1,
                'url' => $uploadDir . $splInfo->getSaveName(),
                'title' => $splInfo->getSaveName(),
                'original' => $fileInfo['name'],
                'type' => $fileInfo['type'],
                'size' => $fileInfo['size']
            ];
        } else {
            // 上传失败获取错误信息
            return ['code' => 0, 'error' => $file->getError()];
        }
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    protected function getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if (is_string($file) && preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen(LANRU . "public" )),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

    /**
     * 拉取远程图片
     * @return mixed
     */
    private function saveRemote($imgUrl, $config)
    {
        $imgUrl = str_replace("&amp;", "&", $imgUrl);

        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            return ['code' => 0, 'error' => 'URL error'];
        }

        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            return ['code' => 0, 'error' => 'URL error'];
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            return ['code' => 0, 'error' => 'IP error'];
        }

        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            return ['code' => 0, 'error' => 'NET error'];
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $config['catcherAllowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            return ['code' => 0, 'error' => '格式 error'];
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);

        $oriName = $m ? $m[1]:"";
        $fileSize = strlen($img);
        $fileType = strtolower(strrchr($oriName, '.'));

        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $config["catcherPathFormat"];
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);

        //过滤文件名的非法自负,并替换文件名
        $oriNameTmp = substr($oriName, 0, strrpos($oriName, '.'));
        $oriNameTmp = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $oriNameTmp);
        $format = str_replace("{filename}", $oriNameTmp, $format);

        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }
        $fullName = $format . $fileType;

        $rootPath = $_SERVER['DOCUMENT_ROOT'];
        if (substr($fullName, 0, 1) != '/') {
            $fullName = '/' . $fullName;
        }
        $filePath = $rootPath . $fullName;

        $fileName = substr($filePath, strrpos($filePath, '/') + 1);
        $dirname = dirname($filePath);

        //检查文件大小是否超出限制
        if ($fileSize > ($config["catcherMaxSize"])) {
            return ['code' => 0, 'error' => 'size error'];
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            return ['code' =>0, 'error' => 'ERROR_CREATE_DIR'];
        } else if (!is_writeable($dirname)) {;
            return ['code' =>0, 'error' => 'ERROR_DIR_NOT_WRITEABLE'];
        }

        //移动文件
        if (!(file_put_contents($filePath, $img) && file_exists($filePath))) { //移动失败
            return ['code' => 0, 'error' => 'ERROR_WRITE_CONTENT'];
        } else { //移动成功
            $imagewidth = $imageheight = 0;
            if (in_array($fileType, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                $imgInfo = getimagesize($filePath);
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }

            $extparam = $this->request->post();
            $params = array(
                'admin_id'    => (int)$this->auth->id,
                'user_id'     => 0,
                'filesize'    => $fileName,
                'imagewidth'  => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype'   => $fileType,
                'imageframes' => 0,
                'mimetype'    => 'image/' . $oriName,
                'url'         => $fullName,
                'uploadtime'  => time(),
                'storage'     => 'local',
                'sha1'        => hash_file('md5', $filePath),
                'extparam'    => json_encode($extparam),
            );
            $attachment = model("attachment");
            $attachment->data(array_filter($params));
            $attachment->save();

            return ['code' => 1, 'state' => 'SUCESS',
                'url' => $fullName, 'size' => $fileSize,
                'title' => $fileName, 'original' => $oriName, 'source' => $imgUrl];
        }

    }
}