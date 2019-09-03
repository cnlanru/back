-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2019-08-01 19:14:58
-- 服务器版本： 5.6.37-log
-- PHP Version: 7.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmsv2`
--

-- --------------------------------------------------------

--
-- 表的结构 `lanru_admin`
--

DROP TABLE IF EXISTS `lanru_admin`;
CREATE TABLE IF NOT EXISTS `lanru_admin` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) DEFAULT '' COMMENT '用户名',
  `password` varchar(32) DEFAULT '' COMMENT '密码',
  `salt` varchar(30) DEFAULT '' COMMENT '密码盐',
  `loginfailure` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` int(10) DEFAULT '0' COMMENT '登录时间',
  `loginip` varchar(20) NOT NULL DEFAULT '' COMMENT '登录 IP',
  `createtime` int(10) DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) DEFAULT '0' COMMENT '更新时间',
  `token` varchar(59) DEFAULT '' COMMENT 'Session标识',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态0禁用 1正常',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `lanru_admin`
--

INSERT INTO `lanru_admin` (`id`, `username`, `password`, `salt`, `loginfailure`, `logintime`, `loginip`, `createtime`, `updatetime`, `token`, `status`) VALUES
(1, 'admin', '9cc953729a9838417c770124446925b2', 's4f3', 0, 1564653730, '', 1492186163, 1564653730, '1c231751-8009-49a7-a2e4-6ca4b17621d7', 1);

-- --------------------------------------------------------

--
-- 表的结构 `lanru_admin_log`
--

DROP TABLE IF EXISTS `lanru_admin_log`;
CREATE TABLE IF NOT EXISTS `lanru_admin_log` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text NOT NULL COMMENT '内容',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(10) DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `name` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员日志表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `lanru_attachment`
--

DROP TABLE IF EXISTS `lanru_attachment`;
CREATE TABLE IF NOT EXISTS `lanru_attachment` (
  `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filesize` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(10) DEFAULT NULL COMMENT '创建日期',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` int(10) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='附件表' ROW_FORMAT=COMPACT;



-- --------------------------------------------------------

--
-- 表的结构 `lanru_auth_group`
--

DROP TABLE IF EXISTS `lanru_auth_group`;
CREATE TABLE IF NOT EXISTS `lanru_auth_group` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='分组表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `lanru_auth_group`
--

INSERT INTO `lanru_auth_group` (`id`, `pid`, `name`, `rules`, `createtime`, `updatetime`, `status`) VALUES
(1, 0, '超级会员组', '*', 1490883540, 149088354, '1'),
(2, 1, '二级管理员', '', NULL, NULL, '0'),
(3, 1, '财务', '', NULL, NULL, '1'),
(4, 1, '编辑', '13,14,16,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,68,69,70,71,72,74,75,76,77,78,80,81,82,83,84,1,6,7,8,2,3,9,10,11,12,5,4,67,73,79,66', NULL, NULL, '1');

-- --------------------------------------------------------

--
-- 表的结构 `lanru_auth_group_access`
--

DROP TABLE IF EXISTS `lanru_auth_group_access`;
CREATE TABLE IF NOT EXISTS `lanru_auth_group_access` (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '会员ID',
  `group_id` int(10) UNSIGNED NOT NULL COMMENT '级别ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限分组表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `lanru_auth_group_access`
--

INSERT INTO `lanru_auth_group_access` (`uid`, `group_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `lanru_auth_rule`
--

DROP TABLE IF EXISTS `lanru_auth_rule`;
CREATE TABLE IF NOT EXISTS `lanru_auth_rule` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1为菜单,0为权限节点',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `createtime` int(10) DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) DEFAULT '0' COMMENT '更新时间',
  `weight` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态 1正常 0隐藏',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `pid` (`pid`),
  KEY `weigh` (`weight`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COMMENT='节点表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `lanru_auth_rule`
--

INSERT INTO `lanru_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weight`, `status`) VALUES
(1, 0, 0, 'index', '控制台', 'fa fa-dashboard', '', 'Dashboard tips', 1, 1497429920, 1564024922, 143, 1),
(2, 0, 0, 'general', '常规管理', 'fa fa-cogs', '', '', 1, 1497429920, 1497430169, 137, 1),
(4, 0, 0, 'addon', '插件管理', 'fa fa-rocket', '', 'Addon tips', 1, 1502035509, 1502035509, 0, 1),
(5, 0, 0, 'auth', '权限管理', 'fa fa-group', '', '', 1, 1497429920, 1497430092, 99, 1),
(6, 0, 2, 'general/config', '系统配置', 'fa fa-cog', '', 'Config tips', 1, 1497429920, 1497430683, 60, 1),
(7, 0, 2, 'general/attachment', '附件管理', 'fa fa-file-image-o', '', 'Attachment tips', 1, 1497429920, 1497430699, 53, 1),
(8, 0, 2, 'general/profile', '个人配置', 'fa fa-user', '', '', 1, 1497429920, 1497429920, 34, 1),
(9, 0, 5, 'auth/admin', '管理员管理', 'fa fa-user', '', 'Admin tips', 1, 1497429920, 1497430320, 118, 1),
(10, 0, 5, 'auth/adminlog', '管理员日志', 'fa fa-list-alt', '', 'Admin log tips', 1, 1497429920, 1497430307, 113, 1),
(11, 0, 5, 'auth/group', '管理组别', 'fa fa-group', '', 'Group tips', 1, 1497429920, 1497429920, 109, 1),
(12, 0, 5, 'auth/rule', '菜单管理', 'fa fa-bars', '', 'Rule tips', 1, 1497429920, 1497430581, 104, 1),
(13, 0, 1, 'dashboard/index', '查看', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 136, 1),
(14, 0, 1, 'dashboard/add', '添加', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 135, 1),
(15, 0, 1, 'dashboard/del', '删除', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 133, 1),
(16, 0, 1, 'dashboard/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 134, 1),
(17, 0, 1, 'dashboard/multi', '批量', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 132, 1),
(18, 0, 6, 'general/config/index', '查看', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 52, 1),
(19, 0, 6, 'general/config/add', '添加', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 51, 1),
(20, 0, 6, 'general/config/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 50, 1),
(21, 0, 6, 'general/config/del', '删除', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 49, 1),
(22, 0, 6, 'general/config/multi', '批量', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 48, 1),
(23, 0, 7, 'general/attachment/index', '查看', 'fa fa-circle-o', '', 'Attachment tips', 0, 1497429920, 1497429920, 59, 1),
(24, 0, 7, 'general/attachment/select', '选择', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 58, 1),
(25, 0, 7, 'general/attachment/add', '添加', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 57, 1),
(26, 0, 7, 'general/attachment/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 56, 1),
(27, 0, 7, 'general/attachment/del', '删除', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 55, 1),
(28, 0, 7, 'general/attachment/multi', '批量', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 54, 1),
(29, 0, 8, 'general/profile/index', '查看', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 33, 1),
(30, 0, 8, 'general/profile/update', '更新', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 32, 1),
(31, 0, 8, 'general/profile/add', '添加', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 31, 1),
(32, 0, 8, 'general/profile/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 30, 1),
(33, 0, 8, 'general/profile/del', '删除', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 29, 1),
(34, 0, 8, 'general/profile/multi', '批量', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 28, 1),
(40, 0, 9, 'auth/admin/index', '查看', 'fa fa-circle-o', '', 'Admin tips', 0, 1497429920, 1497429920, 117, 1),
(41, 0, 9, 'auth/admin/add', '添加', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 116, 1),
(42, 0, 9, 'auth/admin/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 115, 1),
(43, 0, 9, 'auth/admin/del', '删除', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 114, 1),
(44, 0, 10, 'auth/adminlog/index', '查看', 'fa fa-circle-o', '', 'Admin log tips', 0, 1497429920, 1497429920, 112, 1),
(45, 0, 10, 'auth/adminlog/detail', '详情', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 111, 1),
(46, 0, 10, 'auth/adminlog/del', '删除', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 110, 1),
(47, 0, 11, 'auth/group/index', '查看', 'fa fa-circle-o', '', 'Group tips', 0, 1497429920, 1497429920, 108, 1),
(48, 0, 11, 'auth/group/add', '添加', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 107, 1),
(49, 0, 11, 'auth/group/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 106, 1),
(50, 0, 11, 'auth/group/del', '删除', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 105, 1),
(51, 0, 12, 'auth/rule/index', '查看', 'fa fa-circle-o', '', 'Rule tips', 0, 1497429920, 1497429920, 103, 1),
(52, 0, 12, 'auth/rule/add', '添加', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 102, 1),
(53, 0, 12, 'auth/rule/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 101, 1),
(54, 0, 12, 'auth/rule/del', '删除', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 100, 1),
(55, 0, 4, 'addon/index', '查看', 'fa fa-circle-o', '', 'Addon tips', 0, 1502035509, 1502035509, 0, 1),
(56, 0, 4, 'addon/add', '添加', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(57, 0, 4, 'addon/edit', '编辑', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(58, 0, 4, 'addon/del', '删除', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(59, 0, 4, 'addon/local', '本地插件', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(60, 0, 4, 'addon/state', '更新状态', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(61, 0, 4, 'addon/install', '安装', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(62, 0, 4, 'addon/uninstall', '卸载', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(63, 0, 4, 'addon/config', '配置', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(64, 0, 4, 'addon/refresh', '刷新', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(65, 0, 4, 'addon/multi', '批量', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 1),
(66, 0, 0, 'user', '会员管理', 'fa fa-list', '', '', 1, 1516374729, 1564650853, 0, 1),
(67, 0, 66, 'user/user', '会员管理', 'fa fa-user', '', '', 1, 1516374729, 1564650861, 0, 1),
(68, 0, 67, 'user/user/index', '查看', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(69, 0, 67, 'user/user/编辑', '编辑', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(70, 0, 67, 'user/user/添加', '添加', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(71, 0, 67, 'user/user/删除', '删除', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(72, 0, 67, 'user/user/批量', '批量', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(73, 0, 66, 'user/group', '会员分组', 'fa fa-users', '', '', 1, 1516374729, 1564650870, 0, 1),
(74, 0, 73, 'user/group/添加', '添加', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(75, 0, 73, 'user/group/编辑', '编辑', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(76, 0, 73, 'user/group/index', '查看', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(77, 0, 73, 'user/group/删除', '删除', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(78, 0, 73, 'user/group/批量', '批量', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(79, 0, 66, 'user/rule', '会员规则', 'fa fa-circle-o', '', '', 1, 1516374729, 1564650879, 0, 1),
(80, 0, 79, 'user/rule/index', '查看', 'fa fa-circle-o', '', '', 0, 1516374729, 1564650892, 0, 0),
(81, 0, 79, 'user/rule/删除', '删除', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(82, 0, 79, 'user/rule/添加', '添加', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(83, 0, 79, 'user/rule/编辑', '编辑', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0),
(84, 0, 79, 'user/rule/批量', '批量', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `lanru_config`
--

DROP TABLE IF EXISTS `lanru_config`;
CREATE TABLE IF NOT EXISTS `lanru_config` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) NOT NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) NOT NULL DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型:string,text,int,array,datetime,date,file',
  `value` text NOT NULL COMMENT '变量值',
  `content` text NOT NULL COMMENT '变量字典数据',
  `rule` varchar(100) NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='系统配置' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `lanru_config`
--

INSERT INTO `lanru_config` (`id`, `name`, `group`, `title`, `tip`, `type`, `value`, `content`, `rule`, `extend`) VALUES
(1, 'configgroup', 'dictionary', '配置分组', '', 'array', '{\"basic\":\"基础配置\",\"dictionary\":\"字典配置\",\"user\":\"会员配置\"}', '', '', ''),
(2, 'sitename', 'basic', ' 网站标题', '', 'string', '', '', '', ''),
(3, 'filemimetype', 'basic', '文件上传格式', '', 'string', 'jpg,png,bmp,jpeg,gif,zip,rar,xls,xlsx', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `lanru_user`
--

DROP TABLE IF EXISTS `lanru_user`;
CREATE TABLE IF NOT EXISTS `lanru_user` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '组别ID',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) NOT NULL DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `level` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '等级',
  `gender` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date DEFAULT '0000-00-00' COMMENT '生日',
  `money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '余额',
  `score` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '积分',
  `successions` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '连续登录天数',
  `maxsuccessions` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '最大连续登录天数',
  `prevtime` int(10) DEFAULT '0' COMMENT '上次登录时间',
  `logintime` int(10) DEFAULT '0' COMMENT '登录时间',
  `loginip` varchar(50) NOT NULL DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '失败次数',
  `joinip` varchar(50) NOT NULL DEFAULT '' COMMENT '加入IP',
  `jointime` int(10) DEFAULT '0' COMMENT '加入时间',
  `createtime` int(10) DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) DEFAULT '0' COMMENT '更新时间',
  `token` varchar(50) NOT NULL DEFAULT '' COMMENT 'Token',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 1正常 0隐藏',
  `verification` varchar(255) NOT NULL DEFAULT '' COMMENT '验证',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `lanru_user`
--

INSERT INTO `lanru_user` (`id`, `group_id`, `username`, `nickname`, `password`, `salt`, `email`, `mobile`, `avatar`, `level`, `gender`, `birthday`, `money`, `score`, `successions`, `maxsuccessions`, `prevtime`, `logintime`, `loginip`, `loginfailure`, `joinip`, `jointime`, `createtime`, `updatetime`, `token`, `status`, `verification`) VALUES
(1, 1, 'admin', 'admin', 'c13f62012fd6a8fdf06b3452a94430e5', 'rpR6Bv', 'admin@163.com', '13888888888', '/uploads/20190801/b9f09ad8999b6b90d82984ca2a9ff1c5.gif', 0, 255, '0000-00-00', '0.00', 0, 1, 1, 1516170492, 1516171614, '127.0.0.1', 0, '127.0.0.1', 1491461418, 0, 1564653862, '', 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `lanru_user_group`
--

DROP TABLE IF EXISTS `lanru_user_group`;
CREATE TABLE IF NOT EXISTS `lanru_user_group` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '组名',
  `rules` text COMMENT '权限节点',
  `createtime` int(10) DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(10) DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态 1正常 0隐藏',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='会员组表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `lanru_user_group`
--

INSERT INTO `lanru_user_group` (`id`, `name`, `rules`, `createtime`, `updatetime`, `status`) VALUES
(1, '默认组1', '', 1515386468, 1564656741, 1),
(2, '游客', '', 1564656752, 1564656752, 1);

-- --------------------------------------------------------

--
-- 表的结构 `lanru_user_money_log`
--

DROP TABLE IF EXISTS `lanru_user_money_log`;
CREATE TABLE IF NOT EXISTS `lanru_user_money_log` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '会员ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更余额',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更前余额',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更后余额',
  `memo` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `createtime` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员余额变动表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `lanru_user_rule`
--

DROP TABLE IF EXISTS `lanru_user_rule`;
CREATE TABLE IF NOT EXISTS `lanru_user_rule` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) DEFAULT '0' COMMENT '父ID',
  `name` varchar(50) DEFAULT '' COMMENT '名称',
  `title` varchar(50) DEFAULT '' COMMENT '标题',
  `remark` varchar(100) DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) DEFAULT '0' COMMENT '是否菜单',
  `createtime` int(10) DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) DEFAULT '0' COMMENT '更新时间',
  `weight` int(10) DEFAULT '0' COMMENT '权重',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态0禁用 1正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='会员规则表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `lanru_user_rule`
--

INSERT INTO `lanru_user_rule` (`id`, `pid`, `name`, `title`, `remark`, `ismenu`, `createtime`, `updatetime`, `weight`, `status`) VALUES
(8, 0, 'index', '前台', '', 1, 1564656491, 1564656550, 9, 1),
(9, 8, 'user', '会员模块', '', 1, 1564656579, 1564656600, 9, 1),
(10, 0, 'api', 'API接口', '', 1, 1564656626, 1564656626, 10, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
