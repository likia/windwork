
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `windworkdb`
--

-- --------------------------------------------------------

--
-- 表的结构 `wk_act`
--

DROP TABLE IF EXISTS `wk_act`;
CREATE TABLE `wk_act` (
  `mod` varchar(64) NOT NULL DEFAULT '',
  `ctl` varchar(64) NOT NULL DEFAULT '',
  `act` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '功能名称',
  PRIMARY KEY (`mod`,`ctl`,`act`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='已安装模块功能表';

--
-- 转存表中的数据 `wk_act`
--

INSERT INTO `wk_act` (`mod`, `ctl`, `act`, `name`) VALUES
('article', 'admin.cat', 'create', '添加文章栏目'),
('article', 'admin.cat', 'delete', '删除文章栏目'),
('article', 'admin.cat', 'list', '管理文章栏目'),
('article', 'admin.cat', 'update', '编辑文章栏目'),
('article', 'admin.content', 'create', '添加文章'),
('article', 'admin.content', 'delete', '删除文章'),
('article', 'admin.content', 'list', '管理文章'),
('article', 'admin.content', 'update', '编辑文章'),
('article', 'show', 'index', '文章首页'),
('article', 'show', 'item', '文章详情'),
('article', 'show', 'list', '文章列表'),
('article', 'show', 'search', '文章搜索'),
('dev', 'builder', 'addctl', '添加新功能'),
('dev', 'builder', 'addmodel', '添加新功能'),
('dev', 'builder', 'createmod', '创建新模块'),
('dev', 'builder', 'moditem', '模块详情'),
('dev', 'builder', 'modlist', '模块列表'),
('system', 'admin.acl', 'mod', '模块权限设置'),
('system', 'admin.acl', 'role', '角色权限设置'),
('system', 'admin.acl', 'user', '用户详细权限设置'),
('system', 'admin.admincp', 'index', '后台首页'),
('system', 'admin.admincp', 'welcome', '后台欢迎页面'),
('system', 'admin.location', 'create', '添加位置标注点'),
('system', 'admin.location', 'delete', '删除位置标注点'),
('system', 'admin.location', 'list', '管理位置标注点'),
('system', 'admin.location', 'sort', '地区位置标注点'),
('system', 'admin.location', 'update', '管理位置标注点'),
('system', 'admin.menu', 'create', '添加菜单'),
('system', 'admin.menu', 'delete', '删除菜单'),
('system', 'admin.menu', 'list', '管理菜单'),
('system', 'admin.menu', 'update', '管理菜单'),
('system', 'admin.module', 'activate', '启用模块'),
('system', 'admin.module', 'deactivate', '停用模块'),
('system', 'admin.module', 'delete', '删除模块'),
('system', 'admin.module', 'install', '安装模块'),
('system', 'admin.module', 'list', '管理模块'),
('system', 'admin.module', 'uninstall', '卸载模块'),
('system', 'admin.nav', 'create', '添加导航'),
('system', 'admin.nav', 'delete', '删除导航'),
('system', 'admin.nav', 'list', '管理导航'),
('system', 'admin.nav', 'update', '编辑导航'),
('system', 'admin.option', 'list', '系统选项管理'),
('system', 'admin.option', 'msgtpl', '短消息通知模板'),
('system', 'admin.option', 'setattr', '修改系统选项输入类型'),
('system', 'admin.option', 'setting', '系统选项设置'),
('system', 'admin.position', 'create', '添加推荐位'),
('system', 'admin.position', 'delete', '删除推荐位'),
('system', 'admin.position', 'list', '管理推荐位'),
('system', 'admin.position', 'update', '编辑推荐位'),
('system', 'admin.positiondata', 'create', '添加推荐数据'),
('system', 'admin.positiondata', 'delete', '删除推荐数据'),
('system', 'admin.positiondata', 'deletebyposid', '删除推荐位推荐信息'),
('system', 'admin.positiondata', 'list', '管理推荐数据'),
('system', 'admin.positiondata', 'update', '编辑推荐数据'),
('system', 'admin.ui', 'setstyle', '设置网站主题'),
('system', 'admin.uploads', 'getalbumbyrid', '根据关联ID获取相册'),
('system', 'admin.uploads', 'list', '附件管理'),
('system', 'admin.uploads', 'setalbumcover', '设置封面图片'),
('system', 'admin.uploads', 'setimagetype', '设置图片类型'),
('system', 'default', 'index', '网站首页'),
('system', 'district', 'getlistbyupid', '根据上级id获取地区列表'),
('system', 'misc', 'captcha', '验证码'),
('system', 'misc', 'error', '错误信息'),
('system', 'misc', 'message', '提示信息'),
('system', 'misc', 'stats', '统计功能'),
('system', 'tools', 'claercache', '清除缓存'),
('system', 'tools', 'databackup', '数据备份'),
('system', 'tools', 'tableoptimize', '数据表优化'),
('system', 'update', 'index', '系统升级'),
('system', 'uploader', 'create', '附件上传'),
('system', 'uploader', 'delete', '附件删除'),
('system', 'uploader', 'list', '附件管理'),
('system', 'uploader', 'load', '查看附件'),
('system', 'uploader', 'update', '附件编辑'),
('user', 'account', 'bindmobile', '绑定手机号码'),
('user', 'account', 'center', '个人中心'),
('user', 'account', 'forgetpassword', '取回密码'),
('user', 'account', 'login', '登录'),
('user', 'account', 'logout', '注销'),
('user', 'account', 'profile', '个人信息'),
('user', 'account', 'register', '注册'),
('user', 'account', 'resetpassword', '重置密码'),
('user', 'account', 'setpassword', '修改密码'),
('user', 'account', 'unbindmobile', '解绑手机号码'),
('user', 'account', 'verifycode', '获取/验证手机验证码'),
('user', 'active', 'checkin', '签到'),
('user', 'admin.role', 'create', '添加角色'),
('user', 'admin.role', 'delete', '删除角色'),
('user', 'admin.role', 'list', '管理角色'),
('user', 'admin.role', 'update', '管理角色'),
('user', 'admin.user', 'create', '添加用户'),
('user', 'admin.user', 'delete', '删除用户'),
('user', 'admin.user', 'list', '管理用户'),
('user', 'admin.user', 'update', '编辑用户'),
('user', 'oauth.weixin', 'callback', '微信授权返回处理'),
('user', 'oauth.weixin', 'login', '微信授权登录'),
('wx', 'admin.diymenu', 'build', '生成微信自定义菜单列表'),
('wx', 'admin.diymenu', 'create', '添加微信自定义菜单'),
('wx', 'admin.diymenu', 'delete', '删除微信自定义菜单'),
('wx', 'admin.diymenu', 'list', '微信自定义菜单列表'),
('wx', 'admin.diymenu', 'update', '编辑微信自定义菜单');

-- --------------------------------------------------------

--
-- 表的结构 `wk_ad`
--

DROP TABLE IF EXISTS `wk_ad`;
CREATE TABLE `wk_ad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '广告名称',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '广告开始有效时间',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '广告结束有效时间',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `type` enum('text','html','image','flash') NOT NULL DEFAULT 'image',
  `content` text COMMENT '广告内容',
  `displayorder` smallint(4) NOT NULL DEFAULT '99' COMMENT '广告显示排序',
  `enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用广告',
  PRIMARY KEY (`id`),
  KEY `starttime` (`starttime`,`endtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告';

-- --------------------------------------------------------

--
-- 表的结构 `wk_ad_place`
--

DROP TABLE IF EXISTS `wk_ad_place`;
CREATE TABLE `wk_ad_place` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '广告位名称',
  `description` varchar(512) NOT NULL DEFAULT '' COMMENT '广告位说明',
  `width` varchar(4) NOT NULL DEFAULT '' COMMENT '广告位宽（px/%）',
  `height` varchar(4) NOT NULL DEFAULT '' COMMENT '广告位高（px/%）',
  `mode` enum('rand','all') NOT NULL DEFAULT 'rand' COMMENT '显示模式：rand）随机显示一个广告；all：显示该广告位全部广告',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告位';

-- --------------------------------------------------------

--
-- 表的结构 `wk_ad_place_r`
--

DROP TABLE IF EXISTS `wk_ad_place_r`;
CREATE TABLE `wk_ad_place_r` (
  `id` int(10) NOT NULL DEFAULT '0',
  `placeid` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`placeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告-广告位关联';

-- --------------------------------------------------------

--
-- 表的结构 `wk_article`
--

DROP TABLE IF EXISTS `wk_article`;
CREATE TABLE `wk_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `upid` int(10) unsigned NOT NULL DEFAULT '0',
  `slug` varchar(200) NOT NULL DEFAULT '' COMMENT '文章别名',
  `editor` varchar(200) NOT NULL DEFAULT '' COMMENT '责任编辑',
  `author` varchar(200) NOT NULL DEFAULT '' COMMENT '作者',
  `from` varchar(200) NOT NULL DEFAULT '' COMMENT '文章来源网站名称',
  `fromurl` varchar(200) NOT NULL DEFAULT '' COMMENT '文章来源网站',
  `gotourl` varchar(128) NOT NULL DEFAULT '' COMMENT '文章跳转URL，设置后点击文字链接跳到该网站',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章发布时间',
  `title` varchar(512) NOT NULL DEFAULT '' COMMENT '文章标题',
  `keyword` varchar(100) NOT NULL DEFAULT '' COMMENT '文章关键词',
  `description` varchar(512) NOT NULL DEFAULT '' COMMENT '文章摘要',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '文章状态：0草稿 ，1已发布，2回收站',
  `modifiedtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章最后更改时间',
  `userip` varchar(25) NOT NULL DEFAULT '' COMMENT '文章发布者ip',
  `tpl` varchar(255) NOT NULL DEFAULT '' COMMENT '文章模板',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '99',
  `views` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文章访问量',
  `pic` varchar(512) NOT NULL DEFAULT '0' COMMENT '文章封面图片',
  `isoriginal` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是原创',
  `cmts` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `favs` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏/喜欢数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章信息';

-- --------------------------------------------------------

--
-- 表的结构 `wk_article_cat`
--

DROP TABLE IF EXISTS `wk_article_cat`;
CREATE TABLE `wk_article_cat` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `slug` varchar(200) NOT NULL DEFAULT '' COMMENT '文章分类别名，用于URL中',
  `upid` int(10) unsigned NOT NULL DEFAULT '0',
  `displayorder` tinyint(10) unsigned NOT NULL DEFAULT '99',
  `navshow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否在导航中显示',
  `navshowson` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示子分类下的文章',
  `listtpl` varchar(128) NOT NULL DEFAULT '' COMMENT '分类页模板',
  `itemtpl` varchar(128) NOT NULL DEFAULT '' COMMENT '详细页模板',
  `url` varchar(120) NOT NULL DEFAULT '' COMMENT '文章分类URL，如果设置，点击文章栏目后将跳转至该URL',
  `listmode` tinyint(2) NOT NULL DEFAULT '1' COMMENT '文章列表页显示模式： 1）列表分页显示文章； 2）列表显示全部文章； 3）进入最新一篇文章；',
  `listrows` tinyint(3) unsigned NOT NULL DEFAULT '15' COMMENT '栏目页每页显示记录数',
  `keyword` varchar(200) NOT NULL DEFAULT '' COMMENT '栏目关键词',
  `description` varchar(512) NOT NULL DEFAULT '' COMMENT '栏目简介',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `allowdel` tinyint(1) NOT NULL DEFAULT '1' COMMENT '该栏目是否允许删除',
  `itemcount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类主题数',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章分类';

-- --------------------------------------------------------

--
-- 表的结构 `wk_article_content`
--

DROP TABLE IF EXISTS `wk_article_content`;
CREATE TABLE `wk_article_content` (
  `id` bigint(20) unsigned NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章详情';

-- --------------------------------------------------------

--
-- 表的结构 `wk_banned_ip`
--

DROP TABLE IF EXISTS `wk_banned_ip`;
CREATE TABLE `wk_banned_ip` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `ip1` smallint(3) NOT NULL DEFAULT '0',
  `ip2` smallint(3) NOT NULL DEFAULT '0',
  `ip3` smallint(3) NOT NULL DEFAULT '0',
  `ip4` smallint(3) NOT NULL DEFAULT '0',
  `admin` varchar(16) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip1`,`ip2`,`ip3`,`ip4`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='禁止访问的用户ip';

-- --------------------------------------------------------

--
-- 表的结构 `wk_district`
--

DROP TABLE IF EXISTS `wk_district`;
CREATE TABLE `wk_district` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `level` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `usetype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `upid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `displayorder` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `upid` (`upid`,`displayorder`) USING BTREE,
  KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='地区表';

-- --------------------------------------------------------

--
-- 表的结构 `wk_diymenu`
--

DROP TABLE IF EXISTS `wk_diymenu`;
CREATE TABLE `wk_diymenu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `upid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单ID',
  `name` varchar(128) NOT NULL COMMENT '菜单名称',
  `url` varchar(120) NOT NULL DEFAULT '' COMMENT '菜单URL，如果设置值则为链接菜单，否则为关键词回复',
  `keyword` varchar(200) NOT NULL DEFAULT '',
  `isenabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '99' COMMENT '显示排序',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信自定义菜单';

-- --------------------------------------------------------

--
-- 表的结构 `wk_menu`
--

DROP TABLE IF EXISTS `wk_menu`;
CREATE TABLE `wk_menu` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `upid` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单ID',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `url` varchar(200) NOT NULL DEFAULT '',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '99',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `mod` varchar(64) NOT NULL DEFAULT '',
  `ctl` varchar(64) NOT NULL DEFAULT '',
  `act` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='管理员后台菜单';

--
-- 转存表中的数据 `wk_menu`
--

INSERT INTO `wk_menu` (`id`, `upid`, `name`, `url`, `displayorder`, `enabled`, `mod`, `ctl`, `act`) VALUES
(1, 0, '首页', '#', 1, 1, 'system', 'default', 'index'),
(2, 0, '设置', '#', 2, 1, 'system', 'default', 'index'),
(3, 0, '模块', '#', 6, 1, 'system', 'default', 'index'),
(4, 0, '内容', '#', 5, 1, 'system', 'default', 'index'),
(5, 0, '用户', '#', 4, 1, 'system', 'default', 'index'),
(6, 0, '工具', 'system.admin.tools.list', 7, 1, 'system', 'tools', 'list'),
(21, 1, '欢迎', 'system.admin.admincp.welcome', 1, 1, 'system', 'admincp', 'welcome'),
(22, 2, '系统设置', 'system.admin.option.list', 1, 1, 'system', 'manageoption', 'list'),
(23, 2, '后台菜单', 'system.admin.menu.list', 2, 1, 'system', 'managemenu', 'list'),
(24, 2, '导航设置', 'system.admin.nav.list', 50, 1, 'system', 'admin.nav', 'list'),
(25, 2, '广告', 'ad.managead.list', 50, 1, 'ad', 'managead', 'list'),
(31, 3, '模块管理', 'system.admin.module.list', 1, 1, 'system', 'admin.module', 'list'),
(32, 3, '模块开发', 'dev.builder.modlist', 2, 1, 'dev', 'builder', 'modlist'),
(40, 4, '推荐内容', 'system.admin.positiondata.list', 3, 1, 'system', 'admin.positiondata', 'list'),
(41, 4, '管理文章', 'article.admin.content.list', 1, 1, 'article', 'admin.content', 'list'),
(42, 4, '管理栏目', 'article.admin.cat.list', 2, 1, 'article', 'admin.cat', 'list'),
(43, 4, '附件管理', 'system.admin.uploads.list', 50, 1, 'system', 'admin.uploads', 'list'),
(51, 5, '会员', 'user.admin.user.list/type:member', 1, 1, 'user', 'admin.user', 'list'),
(52, 5, '分销员', 'user.admin.user.list/type:biz', 2, 1, 'user', 'admin.user', 'list'),
(53, 5, '管理员', 'user.admin.user.list/type:admin', 3, 1, 'user', 'admin.user', 'list'),
(54, 5, '管理角色', 'user.admin.role.list', 4, 1, 'user', 'managerole', 'list'),
(61, 6, '清除缓存', 'system.admin.tools.clearcache', 50, 1, 'system', 'tools', 'clearcache');

-- --------------------------------------------------------

--
-- 表的结构 `wk_module`
--

DROP TABLE IF EXISTS `wk_module`;
CREATE TABLE `wk_module` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(120) NOT NULL DEFAULT '',
  `usesetting` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '使用系统设置',
  `installtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模块安装时间',
  `installuid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模块安装者uid',
  `version` varchar(32) NOT NULL DEFAULT '' COMMENT '模块版本',
  `author` varchar(80) NOT NULL DEFAULT '' COMMENT '模块开发者',
  `email` varchar(80) NOT NULL DEFAULT '' COMMENT '模块开发者邮箱',
  `siteurl` varchar(200) NOT NULL DEFAULT '' COMMENT '模块维护官网url',
  `copyright` varchar(255) NOT NULL DEFAULT '' COMMENT '模块版权信息',
  `desc` varchar(512) NOT NULL DEFAULT '' COMMENT '模块简介',
  `package` enum('core','option','other') NOT NULL DEFAULT 'option' COMMENT '模块类型，core|option|other',
  `activated` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用模块',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='已安装模块';

--
-- 转存表中的数据 `wk_module`
--

INSERT INTO `wk_module` (`id`, `name`, `usesetting`, `installtime`, `installuid`, `version`, `author`, `email`, `siteurl`, `copyright`, `desc`, `package`, `activated`) VALUES
('article', '文章资讯', 0, 1432512492, 1, '1.0', 'windwork.org', 'cmm@windwork.org', 'http://www.windwork.org', 'Copyright (c) 2008-2014 Windwork Team.', '文章新闻管理及发布', 'core', 1),
('dev', '模块开发工具', 0, 1432512502, 1, '1.0', 'windwork.org', 'cmm@windwork.org', 'http://www.windwork.org', 'Copyright (c) 2008-2014 Windwork Team.', '添加新功能或模块。该模块可以随意禁用和卸载，用时安装即可。', 'other', 1),
('system', '系统环境', 0, 1432512488, 1, '1.0', 'windwork.org', 'cmm@windwork.org', 'http://www.windwork.org', 'Copyright (c) 2008-2014 Windwork Team.', '系统基本功能', 'core', 1),
('user', '用户', 0, 1432512483, 1, '1.0', 'windwork.org', 'cmm@windwork.org', 'http://www.windwork.org', 'Copyright (c) 2008-2014 Windwork Team.', '用户模块相关功能', 'core', 1),
('wx', '微信交互模块', 0, 1432512499, 1, '1.0', '恒辉科技', 'cmpan@qq.com', 'http://www.henghuiit.com', '© 2010-2015 恒辉科技版权所有', '微信接口交互对接', 'option', 1);

-- --------------------------------------------------------

--
-- 表的结构 `wk_nav`
--

DROP TABLE IF EXISTS `wk_nav`;
CREATE TABLE `wk_nav` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `upid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '99',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `mod` varchar(32) NOT NULL DEFAULT '',
  `ctl` varchar(32) NOT NULL DEFAULT '',
  `act` varchar(32) NOT NULL DEFAULT '',
  `dot` varchar(64) NOT NULL DEFAULT '',
  `target` varchar(8) NOT NULL DEFAULT '_self' COMMENT '打开目标：_self,_blank',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='前台导航';

-- --------------------------------------------------------

--
-- 表的结构 `wk_option`
--

DROP TABLE IF EXISTS `wk_option`;
CREATE TABLE `wk_option` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` mediumtext NOT NULL COMMENT '配置项设置值',
  `values` text NOT NULL COMMENT '供选择的选项',
  `type` enum('text','textarea','html','checkbox','radio','select','image','custom') NOT NULL DEFAULT 'text' COMMENT '输入表单类型',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '显示标题',
  `displayline` smallint(3) NOT NULL DEFAULT '1' COMMENT '显示行数，对文本域、复选菜单有效',
  `group` varchar(20) NOT NULL DEFAULT '' COMMENT '设置项分组',
  `note` text COMMENT '说明',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '99',
  `allowedit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可以在后台选项管理进行编辑',
  `isrequired` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg` varchar(120) NOT NULL DEFAULT '' COMMENT '填写表单不符合时提示信息',
  `isarray` tinyint(1) NOT NULL DEFAULT '0' COMMENT '保存的值是否是数组，根据表单提交结果判断，如果是数组值则被系列化后保存，读取的时候先反系列化再返回值',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统设置';

--
-- 转存表中的数据 `wk_option`
--

INSERT INTO `wk_option` (`name`, `value`, `values`, `type`, `title`, `displayline`, `group`, `note`, `displayorder`, `allowedit`, `isrequired`, `msg`, `isarray`) VALUES
('baidu_map_api_key', 'YpR7sKdOCgG5NIgIFh6te03U', '', 'text', '百度地图API密钥', 1, 'operation', NULL, 5, 1, 0, '', 0),
('bugfix', '0', '', 'text', '补丁版本', 1, 'system', NULL, 0, 0, 0, '', 0),
('captcha_enabled_opt', 'a:6:{s:3:"reg";s:3:"reg";s:5:"login";s:5:"login";s:14:"forgetpassword";s:14:"forgetpassword";s:11:"setpassword";s:11:"setpassword";s:9:"guestbook";s:9:"guestbook";s:7:"comment";s:7:"comment";}', 'reg\nlogin\nforgetpassword\nsetpassword\nadmin_login\nguestbook\ncomment', 'checkbox', '验证码启用', 1, 'captcha', '&lt;br /&gt;\n图片验证码可以避免恶意批量评论或提交信息，推荐打开验证码功能。注意: 启用验证码会使得部分操作变得繁琐，建议仅在必需时打开，打钩表示开启图片验证码。', 1, 1, 0, '', 1),
('captcha_height', '26', '', 'text', '验证码图片高度  ', 1, 'captcha', '（范围在 15～50 之间） ', 3, 1, 0, '', 0),
('captcha_level', '2', '1\n2\n3\n4\n', 'select', '验证码安全级别', 1, 'captcha', '验证码级别越高越能防止机器人发内容，但人眼识别难度也越大', 4, 1, 0, '', 0),
('captcha_width', '78', '', 'text', '验证码图片宽度', 1, 'captcha', '（范围在 40～145 之间） ', 2, 1, 0, '', 0),
('cmt_post_status', '1', '0\n1\n-1', 'radio', '新发布评论状态', 1, 'operation', NULL, 0, 1, 0, '', 0),
('comment_captcha_switch', '1', '1=是\r\n0=否', 'radio', '发表评论时需输入验证码', 1, '', NULL, 6, 1, 0, '', 0),
('contact_addr', '', '', 'text', '联系地址', 1, 'contact', '（格式：xx省xx市xx）', 3, 1, 1, '', 0),
('contact_email', '', '', 'text', '联系邮箱', 1, 'contact', NULL, 5, 1, 0, '', 0),
('contact_phone', '', '', 'text', '联系电话', 1, 'contact', '（格式：区号-电话，例如，010-86396433）', 4, 1, 1, '', 0),
('contact_qq', '121169238 346438113  123456', '', 'text', '联系QQ', 1, 'contact', NULL, 0, 1, 0, '', 0),
('contact_zip', '', '', 'text', '联系邮编', 1, 'contact', NULL, 6, 1, 0, '', 0),
('editor_height', '420px', '', 'text', '后台编辑器高度', 1, 'ui', '（单位 px，如用百分比，值需带“%”）', 22, 1, 1, '', 0),
('editor_toolbar_set', 'all', 'default\nall\ncustom\nbasic\nsimple', 'select', '后台编辑器菜单功能', 1, 'ui', '', 20, 0, 0, '', 0),
('editor_width', '710px', '', 'text', '后台编辑器宽度', 1, 'ui', '（单位 px，如用百分比，值需带“%”）', 21, 1, 1, '', 0),
('embed_footer', '', '', 'textarea', '页面尾部嵌入内容', 4, 'system', '（方便实现更多功能，供非专业人士使用。）', 15, 1, 0, '', 0),
('embed_header', '', '', 'textarea', '页面头部嵌入内容', 4, 'system', '（方便实现更多功能，供非专业人士使用。）', 14, 1, 0, '', 0),
('ext_reg_roid', '6', '', 'text', '注册商家默认角色ID', 1, 'user', NULL, 0, 1, 0, '', 0),
('install_time', '2015-05-20 21:09', '', 'text', '系统安装时间', 1, 'system', NULL, 0, 0, 0, '', 0),
('mail_auth', '0', '0\n1', 'radio', '是否需要身份验证', 1, 'email', '发送邮件的smtp是否需要身份验证 （0 不需要， 1 需要）', 1, 1, 0, '', 0),
('mail_from', 'service@henghuiit.com', '', 'text', '发送邮件邮件帐号', 1, 'email', '发送邮件的邮件帐号', 3, 1, 0, '', 0),
('mail_host', 'localhost', '', 'text', '服务器', 1, 'email', '发送邮件的smtp服务器', 2, 1, 0, '', 0),
('mail_name', '单品特卖系统', '', 'text', '发送人名称', 1, 'email', '发送邮件显示的发送人姓名', 4, 1, 0, '', 0),
('mail_pass', '', '', 'text', '登录密码', 1, 'email', '发送邮件的smtp登录密码', 7, 1, 0, '', 0),
('mail_port', '', '', 'text', '发送端口', 1, 'email', '发送邮件的smtp端口', 5, 1, 0, '', 0),
('mail_type', '1', '1\r\n2', 'radio', '', 1, 'email', NULL, 0, 1, 0, '', 0),
('mail_user', '', '', 'text', '发送帐号', 1, 'email', '发送邮件的smtp帐号', 6, 1, 0, '', 0),
('msg_tpl_cancel', '', '', 'html', '订单取消通知模板', 12, 'msgtpl', NULL, 0, 0, 0, '', 0),
('msg_tpl_cancel_enabled', '1', '1=是\r\n0=否', 'radio', '订单取消通知功能开启', 1, 'msgtpl', NULL, 0, 0, 0, '', 0),
('msg_tpl_deliver', '', '', 'html', '订单发货通知模板', 12, 'msgtpl', NULL, 0, 0, 0, '', 0),
('msg_tpl_deliver_enabled', '1', '1=是\r\n0=否', 'radio', '订单确认通知功能开启', 1, 'msgtpl', NULL, 0, 0, 0, '', 0),
('msg_tpl_order_confirm', '', '', 'html', '订单确认通知模板', 12, 'msgtpl', NULL, 0, 0, 0, '', 0),
('msg_tpl_order_confirm_enabled', '1', '1=是\r\n0=否', 'radio', '订单确认通知功能开启', 1, 'msgtpl', NULL, 0, 0, 0, '', 0),
('m_logo', 'static/images/m-logo.gif', '', 'image', '手机版网站LOGO', 1, 'ui', '（请上传 160x36像素的图片，支持jpg、gif、png格式）', 1, 1, 1, '', 0),
('password_length_min', '6', '', 'text', '密码最短位数', 1, 'system', 'Minimum password length 密码最短位数', 0, 0, 0, '', 0),
('register_sn', '', '', 'text', '注册码', 1, 'system', '为了我们能更好的提供服务，请购买我们的产品。', 20, 1, 0, '', 0),
('seo_title', '', '', 'text', 'SEO标题', 1, 'system', NULL, 2, 1, 0, '', 0),
('site_description', '', '', 'textarea', '网站描述', 4, 'system', '（填写网站描述，显示在搜索引擎搜索结果上。请把输入框中的“XXX”改成您商店的名称，其他做相应修改。）', 12, 1, 0, '', 0),
('site_domain', '', '', 'text', '网站网址', 1, 'system', '（默认请留空，请设置已绑定到网站根目录的域名，否则会导致前台无法访问，格式：www.mytos.cn）', 7, 1, 0, '', 0),
('site_icp', '', '', 'text', '网店备案号', 1, 'system', '', 11, 1, 0, '', 0),
('site_keyword', '', '', 'textarea', '网站关键词', 4, 'system', '（填写关键词方便搜索引擎收录，每个关键词用竖线“|”、空格或逗号隔开）', 11, 1, 0, '', 0),
('site_logo', 'static/images/logo.png', '', 'image', '网站LOGO', 1, 'ui', '（请上传 260x60像素的图片，支持jpg、gif、png格式）', 0, 1, 1, '', 0),
('site_m_domain', '', '', 'text', '手机店网址', 1, 'system', '（默认请留空，请设置已绑定到网站根目录的域名，否则会导致手机站无法访问，格式：m.mytos.cn）', 8, 1, 0, '', 0),
('site_name', '单品特卖系统', '', 'text', '网站名称', 2, 'system', '（相当于店面的招牌）', 1, 1, 1, '', 0),
('site_slogan', '单品特卖就是这么牛', '', 'text', '网站副标题', 1, 'system', NULL, 2, 1, 0, '', 0),
('site_welcome', '您好，感谢您关注xxx', '', 'text', '网站欢迎词', 1, 'system', '（该信息将显示在页面顶端部分） ', 10, 1, 1, '', 0),
('stats_allowrefresh', '1', '1\n0', 'radio', '访问统计防刷新', 1, 'operation', '如果启用防刷新，在30秒内将不重复统计用户的访问', 0, 1, 0, '', 0),
('stats_rerecordtime', '600', '', 'text', '统计重复访问页面时间间隔', 1, 'operation', '多少秒后再统计重复访问的页面', 0, 1, 0, '', 0),
('system_filter', '', '', 'text', '过滤器列表', 1, 'system', NULL, 0, 0, 0, '', 1),
('ui_davatar', 'static/images/avatar.png', '', 'image', '默认头像', 1, 'ui', NULL, 0, 1, 0, '', 0),
('ui_img_width', '600', '', 'text', '内容中图片宽', 1, 'ui', 'px', 2, 1, 0, '', 0),
('ui_nopic', 'static/images/nopic.png', '', 'image', '默认缩略图', 1, 'ui', NULL, 0, 1, 0, '', 0),
('ui_theme', 'default', '', 'text', '样式', 1, 'ui', NULL, 0, 0, 0, '', 0),
('ui_tpl', 'default', '', 'text', '模板', 1, 'ui', NULL, 0, 0, 0, '', 0),
('upload_allow_image', 'gif,jpg,jpeg,png', '', 'text', '允许上传的图片类型', 1, 'upload', '用逗号隔开', 0, 1, 0, '', 0),
('upload_allow_type', 'swf,flv,mov,qt,avi,wmv,asf,wma,mid,mp3,mpg,rm,ra,rmvb,ram,wav,gif,jpg,jpeg,png,bmp,docx,doc,rar,zip,txt', '', 'textarea', '允许上传其他文件类型', 3, 'upload', '允许上传的文件类型', 0, 1, 0, '', 0),
('upload_max_size', '10M', '', 'text', '上传最大文件', 1, 'upload', '允许上传的最大文件', 0, 1, 0, '', 0),
('upload_subdir_format', 'Y/m/d', 'Ymd\nYm/d\nY/md\nY/m/d\nY/m/d/H\n\n', 'select', '附件保存路径格式', 1, 'upload', NULL, 0, 1, 0, '', 0),
('url_encode', '0', '1\r\n0', 'radio', '对URL进行编码', 1, 'url', NULL, 5, 1, 0, '', 0),
('url_full', '1', '1\r\n0', 'radio', '是否使用完整URL', 1, 'url', NULL, 4, 1, 0, '', 0),
('url_rewrite_ext', '', '', 'text', 'URL重写后缀', 1, 'url', NULL, 2, 1, 0, '', 0),
('url_rewrite_rule', 'category   article.show.list\r\ncontent     article.show.item\r\napi            pubservice.api.index\r\nwshome   wsite.m.index\r\nwslist       wsite.m.list\r\nwsitem     wsite.m.item\r\nwpage      wsite.page.index\r\nwmhome    wmall.m.index\r\nwmlist        wmall.m.list\r\nwmitem      wmall.m.item\r\nwthome     wtao.m.index\r\nwtlist         wtao.goods.list\r\nwtitem       wtao.m.item\r\npayment    wpayment.api.call\r\nwifijump     freewifi.transfer.jump\r\nwxpay       wpayment.wxpay.index\r\ncreatewxpay       wpayment.wxpay.create\r\nslider         slide.slide.index\r\nlm     eunion.member.index', '', 'textarea', 'url简写规则', 5, 'url', '一行一个重写规则，如： \nlogin user/account/login\nnews article/show/item', 3, 1, 0, '', 0),
('user_allow_reg', '1', '0\n1', 'radio', '开启注册', 1, 'user', '1开启，0关闭', 0, 1, 0, '', 0),
('user_guest_roid', '1', '', 'text', '游客角色组id', 1, 'user', NULL, 21, 1, 0, '', 0),
('user_logerr_allow_times', '10', '5\r\n10\r\n15\r\n20\r\n30\r\n45\r\n60\r\n100', 'select', '允许登录错误次数', 1, 'user', '登录错误达到多少次以后将过一段时间才能继续登录', 5, 1, 0, '', 0),
('user_logerr_wait_time', '20', '5\r\n10\r\n15\r\n20\r\n30\r\n45\r\n60\r\n120\r\n300\r\n720\r\n1440', 'select', '错误登录时间间隔', 1, 'user', '达到连续多次登录错误上限后，过多少分钟才能继续登录', 6, 1, 0, '', 0),
('user_manage_page_rows', '15', '', 'text', '后台用户列表每页显示', 1, 'user', '（用户管理后台每页显示用户数）', 10, 1, 1, '', 0),
('user_reg_pact', '一、总则\r\n1、本协议是会员使用本网站的服务所须遵守的条款和条件。\r\n2、用户在进行注册中点击"提交注册"按钮即表示用户与本网站达成协议，完全接受本协议项下的全部条款，所有协议内容在发布之日后自动生效。\r\n3、本网站有权根据需要修改本协议条款内容，并以网站公示的方式进行公告，不再单独通知会员。变更后的协议一经在网站公布后，立即自动生效。如果如您不同意相关变更，应当立即停止使用本网站服务。如果您继续使用本网站服务的，即表示您接受经修订的协议。', '', 'textarea', '注册条款', 4, 'user', '', 9, 1, 0, '', 0),
('user_reg_roid', '2', '', 'text', '新注册用户角色', 1, 'user', NULL, 20, 1, 0, '', 0),
('user_reg_status', '1', '-2\n-1\n0\n1', 'select', '新注册用户状态', 1, 'user', '', 2, 1, 0, '', 0),
('user_reg_stop_reason', '网站内测中，暂停用户注册！需注册请邮箱(op@danpin.club)联系运营组。', '', 'textarea', '暂停会员注册原因  ', 5, 'user', '（该信息以弹出窗口的形式告知客户暂停原因，非必填项）', 1, 1, 0, '', 0),
('user_use_location', '0', '0\n1', 'radio', '启用用户地理位置', 1, 'user', '需要下载纯真ip数据库放到./data/ipdata.dat', 8, 1, 0, '', 0),
('version', '1.0', '', 'text', '系统版本', 1, 'system', NULL, 0, 0, 0, '', 0),
('watermark_enabled', '0', '0\n1', 'radio', '上传图片打水印', 1, 'ui', '（开启该功能后，上传图片后将自动在图片上打上水印图片。）', 8, 1, 0, '', 0),
('watermark_height', '200', '', 'text', '打水印最小图片高', 1, 'ui', '图像高超过该参数以上的图片打水印', 12, 1, 0, '', 0),
('watermark_img', 'static/images/watermark.png', '', 'image', '水印图片', 1, 'ui', '建议使用png图片', 10, 1, 0, '', 0),
('watermark_pos', '9', '1\n2\n3\n4\n5\n6\n7\n8\n9', 'radio', '水印位置', 1, 'ui', '', 9, 1, 0, '', 0),
('watermark_quality', '100', '', 'text', '水印质量', 1, 'ui', '图片清晰度', 11, 1, 0, '', 0),
('watermark_width', '200', '', 'text', '打水印最小图片宽', 1, 'ui', '图像宽超过该参数以上的图片打水印', 13, 1, 0, '', 0),
('wtao_sync_time', '8', '', 'text', '淘宝商品同步时间间隔', 1, 'operation', '(小时) 防止频繁抓取淘宝内容被禁止访问', 0, 1, 0, '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `wk_option_group`
--

DROP TABLE IF EXISTS `wk_option_group`;
CREATE TABLE `wk_option_group` (
  `id` varchar(32) NOT NULL,
  `name` varchar(80) NOT NULL DEFAULT '',
  `description` varchar(512) NOT NULL DEFAULT '',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '从小到大排列',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统设置分组';

--
-- 转存表中的数据 `wk_option_group`
--

INSERT INTO `wk_option_group` (`id`, `name`, `description`, `displayorder`, `enabled`) VALUES
('captcha', '验证码设置', '', 7, 1),
('contact', '联系方式', '网站联系方式', 5, 1),
('email', '邮箱设置', '系统用来发送确认注册、更改新的秘密等邮件的email账号', 9, 1),
('operation', '运营设置', '', 4, 1),
('system', '基本设置', '系统通用全局设置', 0, 1),
('ui', '显示设置', '', 6, 1),
('upload', '上传设置', '上传附件设置', 8, 1),
('url', '链接设置', '', 10, 1),
('user', '用户设置', '用户相关设置', 7, 1);

-- --------------------------------------------------------

--
-- 表的结构 `wk_position`
--

DROP TABLE IF EXISTS `wk_position`;
CREATE TABLE `wk_position` (
  `id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '推荐置id',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '推荐位名称',
  `shownum` smallint(4) unsigned NOT NULL DEFAULT '8' COMMENT '推荐位显示商品数量',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '99' COMMENT '推荐位显示顺序',
  `description` varchar(512) NOT NULL DEFAULT '' COMMENT '推荐位简介',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品推荐位';

-- --------------------------------------------------------

--
-- 表的结构 `wk_position_data`
--

DROP TABLE IF EXISTS `wk_position_data`;
CREATE TABLE `wk_position_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `posid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `pic` varchar(512) NOT NULL DEFAULT '' COMMENT '缩略图路径',
  `picw` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '封面图片宽',
  `pich` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '封面图片高',
  `title` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(512) NOT NULL DEFAULT '',
  `url` varchar(128) NOT NULL DEFAULT '',
  `pushtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推荐时间',
  `modifytime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最新修改时间',
  `displayorder` smallint(4) NOT NULL DEFAULT '99',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '内容类型',
  `itemid` varchar(64) NOT NULL DEFAULT '' COMMENT '内容主键值，修改推荐位信息时根据type,item来识别',
  PRIMARY KEY (`id`),
  KEY `itemid` (`type`,`itemid`),
  KEY `posid` (`posid`,`displayorder`,`pushtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐位数据';

-- --------------------------------------------------------

--
-- 表的结构 `wk_shipping_area`
--

DROP TABLE IF EXISTS `wk_shipping_area`;
CREATE TABLE `wk_shipping_area` (
  `areaid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '配送方式名称',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '配送方式编码',
  `cfg` varchar(2048) NOT NULL DEFAULT '',
  PRIMARY KEY (`areaid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='配送区域';

-- --------------------------------------------------------

--
-- 表的结构 `wk_shipping_region`
--

DROP TABLE IF EXISTS `wk_shipping_region`;
CREATE TABLE `wk_shipping_region` (
  `districtid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所在地区id',
  `areaid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加的配送区域ID',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '配送方式编码，冗余字段',
  PRIMARY KEY (`districtid`,`areaid`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='配送区域关联';

-- --------------------------------------------------------

--
-- 表的结构 `wk_shipping_setting`
--

DROP TABLE IF EXISTS `wk_shipping_setting`;
CREATE TABLE `wk_shipping_setting` (
  `code` varchar(20) NOT NULL DEFAULT '',
  `insure` decimal(4,3) NOT NULL DEFAULT '0.000' COMMENT '保价费率',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='配送方式设置';

-- --------------------------------------------------------

--
-- 表的结构 `wk_stats`
--

DROP TABLE IF EXISTS `wk_stats`;
CREATE TABLE `wk_stats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(256) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `agent` varchar(64) NOT NULL DEFAULT '' COMMENT '来访者浏览器类型',
  `isrobot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是搜索引擎',
  `os` varchar(32) NOT NULL DEFAULT '' COMMENT '来访者操作系统',
  `lang` varchar(32) NOT NULL DEFAULT '' COMMENT '来访者客户端使用语言',
  `referer` varchar(256) NOT NULL DEFAULT '' COMMENT '来源页面',
  `agentlog` varchar(200) NOT NULL DEFAULT '' COMMENT '未知agent记录$_SERVER[''HTTP_USER_AGENT'']',
  PRIMARY KEY (`id`),
  KEY `uri` (`uri`(255),`ip`,`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访问统计';

-- --------------------------------------------------------

--
-- 表的结构 `wk_upload`
--

DROP TABLE IF EXISTS `wk_upload`;
CREATE TABLE `wk_upload` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rid` char(36) NOT NULL DEFAULT '',
  `type` varchar(36) NOT NULL DEFAULT 'content' COMMENT '附件类型',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '在同一个主题中显示的排序',
  `group` varchar(16) NOT NULL DEFAULT '' COMMENT '附加分组，比如在同一个主题中区分不同的相册',
  `isimage` tinyint(1) NOT NULL DEFAULT '0',
  `isflash` tinyint(1) NOT NULL DEFAULT '0',
  `isvideo` tinyint(1) NOT NULL DEFAULT '0',
  `isaudio` tinyint(1) NOT NULL DEFAULT '0',
  `isfile` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `note` varchar(512) NOT NULL DEFAULT '' COMMENT '附件备注、说明',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` bigint(20) NOT NULL DEFAULT '0',
  `md5` varchar(32) NOT NULL DEFAULT '',
  `mime` varchar(50) NOT NULL DEFAULT '',
  `size` bigint(20) NOT NULL DEFAULT '0' COMMENT '附件尺寸，单位 bit',
  PRIMARY KEY (`id`),
  KEY `getAlbumByRid` (`rid`,`isimage`,`type`,`displayorder`,`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='上传文件表';

--
-- 转存表中的数据 `wk_upload`
--

INSERT INTO `wk_upload` (`id`, `rid`, `type`, `displayorder`, `group`, `isimage`, `isflash`, `isvideo`, `isaudio`, `isfile`, `name`, `path`, `note`, `dateline`, `uid`, `md5`, `mime`, `size`) VALUES
(1, '17b7b960-8a91-d8c9-55ec-2b02a298', 'undefined', 0, '', 1, 0, 0, 0, 0, '架构图.jpg', '2015/10/07/c77f56053b666b16.jpg', '', 1444186431, 1, '4ce63969847fe29eeeea21db948d7935', 'image/jpeg', 185023),
(2, '17b7b960-8a91-d8c9-55ec-2b02a298', 'undefined', 0, '', 1, 0, 0, 0, 0, '架构图.jpg', '2015/10/07/637b314ae131f0bb.jpg', '', 1444186438, 1, '4ce63969847fe29eeeea21db948d7935', 'image/jpeg', 185023),
(3, '17b7b960-8a91-d8c9-55ec-2b02a298', 'undefined', 0, '', 1, 0, 0, 0, 0, '架构图.jpg', '2015/10/07/b41fa2c9b6e629b1.jpg', '', 1444186631, 1, '4ce63969847fe29eeeea21db948d7935', 'image/jpeg', 185023),
(4, '17b7b960-8a91-d8c9-55ec-2b02a298', 'undefined', 0, '', 1, 0, 0, 0, 0, '架构图.jpg', '2015/10/07/071ab778d77c9704.jpg', '', 1444186664, 1, '4ce63969847fe29eeeea21db948d7935', 'image/jpeg', 185023),
(5, '17b7b960-8a91-d8c9-55ec-2b02a298', 'undefined', 0, '', 1, 0, 0, 0, 0, '架构图.jpg', '2015/10/07/ed928caaa09636d0.jpg', '', 1444186694, 1, '4ce63969847fe29eeeea21db948d7935', 'image/jpeg', 185023),
(6, '17b7b960-8a91-d8c9-55ec-2b02a298', 'undefined', 0, '', 1, 0, 0, 0, 0, '架构图.jpg', '2015/10/07/be4fc713666d89f1.jpg', '', 1444186719, 1, '4ce63969847fe29eeeea21db948d7935', 'image/jpeg', 185023);

-- --------------------------------------------------------

--
-- 表的结构 `wk_user`
--

DROP TABLE IF EXISTS `wk_user`;
CREATE TABLE `wk_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inviteuid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '介绍人ID',
  `type` enum('admin','ext','member') NOT NULL COMMENT '用户类型，member）会员；ext）扩展组，可以是商城卖家，也可以是cms的编辑等；admin）管理员。同role_type，只能选择role_type相同的角色。',
  `uname` varchar(80) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `salt` varchar(6) NOT NULL DEFAULT '' COMMENT '密码加密盐值',
  `acl` text COMMENT '用户权限信息',
  `role` varchar(128) NOT NULL DEFAULT '' COMMENT '系列化数组，一个用户可以拥有多个角色',
  `email` varchar(64) NOT NULL DEFAULT '',
  `sex` tinyint(1) NOT NULL DEFAULT '2' COMMENT '性别：男）1；女）0；保密）2',
  `birthyear` int(4) unsigned zerofill NOT NULL DEFAULT '0000' COMMENT '用户出生年',
  `birthmonth` tinyint(2) unsigned zerofill NOT NULL DEFAULT '00' COMMENT '用户出生月',
  `birthday` tinyint(2) unsigned zerofill NOT NULL DEFAULT '00' COMMENT '用户出生月',
  `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '手机号码',
  `realname` varchar(128) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '昵称',
  `department` varchar(128) NOT NULL DEFAULT '' COMMENT '部门',
  `regdateline` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `locale` varchar(16) NOT NULL DEFAULT 'zh_CN' COMMENT '使用语言',
  `ispub` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否公开个人资料',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态，0：未审核；1：已审核；2：禁止发言；3：禁止访问',
  `isextvalid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '扩展组信息是否已审核',
  `description` varchar(512) NOT NULL DEFAULT '',
  `postdenied` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止该用户发布内容',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `avatarid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '头像上传图片的id',
  PRIMARY KEY (`uid`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户';

--
-- 转存表中的数据 `wk_user`
--

INSERT INTO `wk_user` (`uid`, `inviteuid`, `type`, `uname`, `password`, `salt`, `acl`, `role`, `email`, `sex`, `birthyear`, `birthmonth`, `birthday`, `mobile`, `realname`, `nickname`, `department`, `regdateline`, `locale`, `ispub`, `status`, `isextvalid`, `description`, `postdenied`, `avatar`, `avatarid`) VALUES
(1, 0, 'admin', 'admin', 'd46a374f9991106fdfdeb8e50b08171a', '7fbde6', NULL, '4', 'admin@admin.com', 2, 0000, 00, 00, '', '', 'admin', '', 1432511896, 'zh_CN', 1, 1, 0, '', 0, '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `wk_user_acl`
--

DROP TABLE IF EXISTS `wk_user_acl`;
CREATE TABLE `wk_user_acl` (
  `roid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `mod` varchar(64) NOT NULL DEFAULT '',
  `ctl` varchar(64) NOT NULL DEFAULT '',
  `act` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`roid`,`uid`,`mod`,`ctl`,`act`),
  KEY `uid` (`uid`),
  KEY `roid` (`roid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户权限控制列表';

--
-- 转存表中的数据 `wk_user_acl`
--

INSERT INTO `wk_user_acl` (`roid`, `uid`, `mod`, `ctl`, `act`) VALUES
(1, 0, 'article', 'show', 'index'),
(1, 0, 'article', 'show', 'item'),
(1, 0, 'article', 'show', 'list'),
(1, 0, 'article', 'show', 'search'),
(1, 0, 'system', 'default', 'index'),
(1, 0, 'system', 'district', 'getlistbyupid'),
(1, 0, 'system', 'misc', 'captcha'),
(1, 0, 'system', 'misc', 'error'),
(1, 0, 'system', 'misc', 'message'),
(1, 0, 'system', 'misc', 'stats'),
(1, 0, 'system', 'uploader', 'load'),
(1, 0, 'user', 'account', 'forgetpassword'),
(1, 0, 'user', 'account', 'login'),
(1, 0, 'user', 'account', 'logout'),
(1, 0, 'user', 'account', 'register'),
(1, 0, 'user', 'account', 'resetpassword'),
(1, 0, 'user', 'oauth.weixin', 'callback'),
(1, 0, 'user', 'oauth.weixin', 'login'),
(2, 0, 'article', 'show', 'index'),
(2, 0, 'article', 'show', 'item'),
(2, 0, 'article', 'show', 'list'),
(2, 0, 'article', 'show', 'search'),
(2, 0, 'system', 'default', 'index'),
(2, 0, 'system', 'district', 'getlistbyupid'),
(2, 0, 'system', 'misc', 'captcha'),
(2, 0, 'system', 'misc', 'error'),
(2, 0, 'system', 'misc', 'message'),
(2, 0, 'system', 'misc', 'stats'),
(2, 0, 'system', 'uploader', 'create'),
(2, 0, 'system', 'uploader', 'delete'),
(2, 0, 'system', 'uploader', 'list'),
(2, 0, 'system', 'uploader', 'load'),
(2, 0, 'system', 'uploader', 'thumb'),
(2, 0, 'system', 'uploader', 'update'),
(2, 0, 'user', 'account', 'bindmobile'),
(2, 0, 'user', 'account', 'center'),
(2, 0, 'user', 'account', 'forgetpassword'),
(2, 0, 'user', 'account', 'login'),
(2, 0, 'user', 'account', 'logout'),
(2, 0, 'user', 'account', 'profile'),
(2, 0, 'user', 'account', 'register'),
(2, 0, 'user', 'account', 'resetpassword'),
(2, 0, 'user', 'account', 'setpassword'),
(2, 0, 'user', 'account', 'unbindmobile'),
(2, 0, 'user', 'account', 'verifycode'),
(2, 0, 'user', 'active', 'checkin'),
(2, 0, 'user', 'oauth.weixin', 'callback'),
(2, 0, 'user', 'oauth.weixin', 'login'),
(3, 0, 'article', 'show', 'index'),
(3, 0, 'article', 'show', 'item'),
(3, 0, 'article', 'show', 'list'),
(3, 0, 'article', 'show', 'search'),
(3, 0, 'system', 'default', 'index'),
(3, 0, 'system', 'district', 'getlistbyupid'),
(3, 0, 'system', 'misc', 'captcha'),
(3, 0, 'system', 'misc', 'error'),
(3, 0, 'system', 'misc', 'message'),
(3, 0, 'system', 'misc', 'stats'),
(3, 0, 'system', 'uploader', 'create'),
(3, 0, 'system', 'uploader', 'delete'),
(3, 0, 'system', 'uploader', 'list'),
(3, 0, 'system', 'uploader', 'load'),
(3, 0, 'system', 'uploader', 'thumb'),
(3, 0, 'system', 'uploader', 'update'),
(3, 0, 'user', 'account', 'bindmobile'),
(3, 0, 'user', 'account', 'center'),
(3, 0, 'user', 'account', 'forgetpassword'),
(3, 0, 'user', 'account', 'login'),
(3, 0, 'user', 'account', 'logout'),
(3, 0, 'user', 'account', 'profile'),
(3, 0, 'user', 'account', 'register'),
(3, 0, 'user', 'account', 'resetpassword'),
(3, 0, 'user', 'account', 'setpassword'),
(3, 0, 'user', 'account', 'unbindmobile'),
(3, 0, 'user', 'account', 'verifycode'),
(3, 0, 'user', 'active', 'checkin'),
(3, 0, 'user', 'oauth.weixin', 'callback'),
(3, 0, 'user', 'oauth.weixin', 'login'),
(4, 0, 'article', 'admin.cat', 'create'),
(4, 0, 'article', 'admin.cat', 'delete'),
(4, 0, 'article', 'admin.cat', 'list'),
(4, 0, 'article', 'admin.cat', 'update'),
(4, 0, 'article', 'admin.content', 'create'),
(4, 0, 'article', 'admin.content', 'delete'),
(4, 0, 'article', 'admin.content', 'list'),
(4, 0, 'article', 'admin.content', 'update'),
(4, 0, 'article', 'show', 'index'),
(4, 0, 'article', 'show', 'item'),
(4, 0, 'article', 'show', 'list'),
(4, 0, 'article', 'show', 'search'),
(4, 0, 'system', 'admin.admincp', 'index'),
(4, 0, 'system', 'admin.admincp', 'welcome'),
(4, 0, 'system', 'admin.location', 'create'),
(4, 0, 'system', 'admin.location', 'delete'),
(4, 0, 'system', 'admin.location', 'list'),
(4, 0, 'system', 'admin.location', 'sort'),
(4, 0, 'system', 'admin.location', 'update'),
(4, 0, 'system', 'admin.position', 'create'),
(4, 0, 'system', 'admin.position', 'delete'),
(4, 0, 'system', 'admin.position', 'list'),
(4, 0, 'system', 'admin.position', 'update'),
(4, 0, 'system', 'admin.positiondata', 'create'),
(4, 0, 'system', 'admin.positiondata', 'delete'),
(4, 0, 'system', 'admin.positiondata', 'deletebyposid'),
(4, 0, 'system', 'admin.positiondata', 'list'),
(4, 0, 'system', 'admin.positiondata', 'update'),
(4, 0, 'system', 'admin.uploads', 'getalbumbyrid'),
(4, 0, 'system', 'admin.uploads', 'list'),
(4, 0, 'system', 'admin.uploads', 'setalbumcover'),
(4, 0, 'system', 'admin.uploads', 'setimagetype'),
(4, 0, 'system', 'default', 'index'),
(4, 0, 'system', 'district', 'getlistbyupid'),
(4, 0, 'system', 'misc', 'captcha'),
(4, 0, 'system', 'misc', 'error'),
(4, 0, 'system', 'misc', 'message'),
(4, 0, 'system', 'misc', 'stats'),
(4, 0, 'system', 'tools', 'claercache'),
(4, 0, 'system', 'uploader', 'create'),
(4, 0, 'system', 'uploader', 'delete'),
(4, 0, 'system', 'uploader', 'list'),
(4, 0, 'system', 'uploader', 'load'),
(4, 0, 'system', 'uploader', 'thumb'),
(4, 0, 'system', 'uploader', 'update'),
(4, 0, 'user', 'account', 'bindmobile'),
(4, 0, 'user', 'account', 'center'),
(4, 0, 'user', 'account', 'forgetpassword'),
(4, 0, 'user', 'account', 'login'),
(4, 0, 'user', 'account', 'logout'),
(4, 0, 'user', 'account', 'profile'),
(4, 0, 'user', 'account', 'register'),
(4, 0, 'user', 'account', 'resetpassword'),
(4, 0, 'user', 'account', 'setpassword'),
(4, 0, 'user', 'account', 'unbindmobile'),
(4, 0, 'user', 'account', 'verifycode'),
(4, 0, 'user', 'active', 'checkin'),
(4, 0, 'user', 'oauth.weixin', 'callback'),
(4, 0, 'user', 'oauth.weixin', 'login'),
(4, 0, 'wx', 'admin.diymenu', 'build'),
(4, 0, 'wx', 'admin.diymenu', 'create'),
(4, 0, 'wx', 'admin.diymenu', 'delete'),
(4, 0, 'wx', 'admin.diymenu', 'list'),
(4, 0, 'wx', 'admin.diymenu', 'update');

-- --------------------------------------------------------

--
-- 表的结构 `wk_user_address`
--

DROP TABLE IF EXISTS `wk_user_address`;
CREATE TABLE `wk_user_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `mobile` varchar(100) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(64) NOT NULL DEFAULT '',
  `zipcode` varchar(6) NOT NULL DEFAULT '' COMMENT '邮编',
  `provinceid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '省',
  `provincename` varchar(128) NOT NULL DEFAULT '',
  `cityid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '市',
  `cityname` varchar(128) NOT NULL DEFAULT '',
  `districtid` int(10) unsigned NOT NULL COMMENT '县/区',
  `districtname` varchar(128) NOT NULL DEFAULT '',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '地址详情',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '99',
  `isdefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是默认地址',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`displayorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户收货地址';

-- --------------------------------------------------------

--
-- 表的结构 `wk_user_belong_role`
--

DROP TABLE IF EXISTS `wk_user_belong_role`;
CREATE TABLE `wk_user_belong_role` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `roid` mediumint(8) NOT NULL DEFAULT '0' COMMENT '角色ID',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期',
  PRIMARY KEY (`uid`,`roid`),
  KEY `roid` (`roid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户所属角色表';

--
-- 转存表中的数据 `wk_user_belong_role`
--

INSERT INTO `wk_user_belong_role` (`uid`, `roid`, `endtime`) VALUES
(1, 4, 0);

-- --------------------------------------------------------

--
-- 表的结构 `wk_user_log`
--

DROP TABLE IF EXISTS `wk_user_log`;
CREATE TABLE `wk_user_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(64) NOT NULL DEFAULT '' COMMENT '用户操作功能',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '用户登录IP',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户操作日志';

-- --------------------------------------------------------

--
-- 表的结构 `wk_user_reset_password`
--

DROP TABLE IF EXISTS `wk_user_reset_password`;
CREATE TABLE `wk_user_reset_password` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `applytime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提交取回密码信息的时间',
  `hash` varchar(32) NOT NULL DEFAULT '' COMMENT '重置密码的哈希值',
  PRIMARY KEY (`uid`,`applytime`)
) ENGINE=InnoDB DEFAULT CHARSET=ucs2 COMMENT='重置密码表';

-- --------------------------------------------------------

--
-- 表的结构 `wk_user_role`
--

DROP TABLE IF EXISTS `wk_user_role`;
CREATE TABLE `wk_user_role` (
  `roid` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '',
  `type` enum('admin','biz','member','guest') NOT NULL DEFAULT 'member' COMMENT '角色类型，guest只作为游客权限设置，没有任何用户属于该角色',
  `description` varchar(512) NOT NULL DEFAULT '' COMMENT '角色介绍信息',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁用该角色',
  `displayorder` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '角色显示排序',
  `allowdel` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `allowselect` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '在选择用户角色时是否显示并允许选择该角色',
  PRIMARY KEY (`roid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户角色';

--
-- 转存表中的数据 `wk_user_role`
--

INSERT INTO `wk_user_role` (`roid`, `name`, `type`, `description`, `disabled`, `displayorder`, `allowdel`, `allowselect`) VALUES
(1, '游客', 'guest', '特殊角色，没有登录的游客将设置为该组角色', 0, 99, 0, 0),
(2, '普通会员', 'member', '', 0, 1, 0, 1),
(3, 'VIP会员', 'member', '', 0, 2, 0, 1),
(4, '系统管理员', 'admin', '', 0, 1, 0, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
