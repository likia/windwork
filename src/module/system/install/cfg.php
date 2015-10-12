<?php

return array (	
    'acts' => array (
    	// 管理后台首页
        'admin.admincp' => array(
	        'index'   => '后台首页',
	        'welcome' => '后台欢迎页面',
        ),
    	
		// 模块管理
        'admin.module' => array(
            'list'        => '管理模块',
            'activate'    => '启用模块',  // 已安装但被停用了需启用才能使用
            'deactivate'  => '停用模块',  // 禁用
            'install'     => '安装模块',  // 尚未安装的需安装才能使用
            'uninstall'   => '卸载模块',  // 卸载，包括数据库中该模块相关的数据将被移除
            'delete'      => '删除模块',
        ),
    		
    	'admin.acl' => array(
    		'mod'   => '模块权限设置',
    		'role'  => '角色权限设置',
    		'user'  => '用户详细权限设置',
    	),
		
    	// 界面设置
        'admin.ui' => array(
            'setstyle'    => '设置网站主题',
        ),
    	
		// 杂项
        'misc' => array(
            'captcha'   => '验证码',
        	'message'   => '提示信息',
        	'error'     => '错误信息',
        	'stats'     => '统计功能',
        ),
    	
		// 系统升级
        'update' => array(
            'index'   => '系统升级',
        ),
    	
        // 后台菜单管理
        'admin.menu' => array(
        	'list'     => '管理菜单',
        	'create'   => '添加菜单',
        	'update'   => '管理菜单',
        	'delete'   => '删除菜单',
        ),
    		
        // 前台导航管理
        'admin.nav' => array(
        	'list'     => '管理导航',
        	'create'   => '添加导航',
        	'update'   => '管理导航',
        	'delete'   => '删除导航',
        ),
    	    		
    	// 地理位置标注点管理
        'admin.location' => array(
            'list'     => '管理位置标注点',
            'create'   => '添加位置标注点',
            'update'   => '管理位置标注点',
            'delete'   => '删除位置标注点',
            'sort'     => '地区位置标注点',
        ),
    	
    	// 导航管理
        'admin.nav' => array(
            'list'     => '管理导航',
            'create'   => '添加导航',
            'update'   => '编辑导航',
            'delete'   => '删除导航',
        ),
    	
    	// 导航推荐位
        'admin.position' => array(
            'list'     => '管理推荐位',
            'create'   => '添加推荐位',
            'update'    => '编辑推荐位',
            'delete'   => '删除推荐位',
        ),
		
    	// 导航推荐位
        'admin.positiondata' => array(
            'list'          => '管理推荐数据',
            'create'        => '添加推荐数据',
            'update'        => '编辑推荐数据',
            'delete'        => '删除推荐数据',
            'deletebyposid' => '删除推荐位推荐信息',
        ),
    	
    	// 导航管理
        'tools' => array(
            'tableoptimize' => '数据表优化',
            'databackup'    => '数据备份',
            'databackup'    => '数据备份',
            'claercache'    => '清除缓存',
        ),
    	
        // 系统设置
        'admin.option' => array(
            'list'      => '系统选项管理',
            'setattr'   => '修改系统选项输入类型',
            'setting'   => '系统选项设置',
            'msgtpl'    => '短消息通知模板',
        ),

    	// 附件上传
        'uploader' => array(
            'create'        => '附件上传',
            'update'        => '附件编辑',
            'delete'        => '附件删除',
            'list'          => '附件管理',
            'load'          => '查看附件', 
        ),
    		
    	// 附件管理
        'admin.uploads' => array(
            'list'          => '附件管理',
            'getAlbumByRid' => '根据关联ID获取相册', 
            'setAlbumCover' => '设置封面图片',
            'setImageType'  => '设置图片类型',
        ),
    	
		// 前台默认控制器
		'default' => array(
		    'index'     => '网站首页',
        ),
    	
		// 地区
		'district' => array(
		    'getListByUpid'       => '根据上级id获取地区列表',
        ),
	),
	
	'acls' => array(        
    	// 管理后台首页
        'admin.admincp' => array(
	        'index'     => 1,
	        'welcome'   => 1,
        ),
    	
		// 模块管理
        'admin.module' => array(
		),
		
    	// 界面设置
        'ui' => array(
            //'setStyle'  => 1,
        ),

		// 后台菜单管理，只有超级管理员能操作
		/*
		'admin.menu' => array(
		    'list'           => 0,
            'create'         => 0,
            'update'         => 0,
            'delete'         => 0,
		),
		
		// 导航管理
		'admin.nav' => array(
		    'list'           => 0,
            'create'         => 0,
            'update'         => 0,
            'delete'         => 0,
		),
		*/
		
		'admin.location' => array(
			'list'           => 1,
			'create'         => 1,
			'update'         => 1,
			'delete'         => 1,
			'sort'           => 1,
		),

		'admin.position' => array(
		    'list'           => 1,
            'create'         => 1,
            'update'         => 1,
            'delete'         => 1,
		),
		
		'admin.positiondata' => array(
		    'list'           => 1,
            'create'         => 1,
            'update'         => 1,
            'delete'         => 1,
            'deletebyposid'  => 1,
		),
			
		'tools' => array(
		    'claerCache'     => 1,
		),
        
		'admin.uploads' => array(
			'list'   => 2,
        	'setImageType'   => 2,
            'getAlbumByRid'  => 2,
            'setAlbumCover'  => 2,
		),
			
    	// 附件上传
        'uploader' => array(
            'create'         => 3,
            'update'         => 3,
            'delete'         => 3,
            'list'           => 3,
            'load'           => 4,
            'thumb'          => 3,
        ),
    	
		// 前台默认控制器
		'default' => array(
		    'index'       => '*',
        ),
		
		'misc' => array(
            'captcha'    => '*',
            'message'    => '*',
            'error'      => '*',
            'stats'      => '*',
		),
    	
		// 地区
		'district' => array(
		    'getListByUpid'       => 4,
        ),
	),
);
