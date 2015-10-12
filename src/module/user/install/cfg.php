<?php

return array (	
    'acts' => array (    		
        // 管理用户
        'admin.user' => array(
            'list'          => '管理用户',
            'create'        => '添加用户',
            'update'        => '编辑用户',
            'delete'        => '删除用户',
        ),
        
        // 管理角色
        'admin.role' => array(
        	'list'     => '管理角色',
        	'create'   => '添加角色',
        	'update'   => '管理角色',
        	'delete'   => '删除角色',
        ),
    		
    	'account' => array(
        	'register'        => '注册',
        	'login'           => '登录',
        	'logout'          => '注销',
        	'profile'         => '个人信息',
        	'center'          => '个人中心',
        	'forgetpassword'  => '取回密码',
        	'resetpassword'   => '重置密码',
    		'setpassword'     => '修改密码', // 登录后根据旧密码修改
    	),
    		
    	'oauth.weixin' => array(
    		'login'		=>	'微信授权登录',
    		'callback'	=>	'微信授权返回处理'
    	),
	),
	
	'acls' => array(        
        // 栏目管理
        'admin.user' => array(
            'list'          => 0,
            'create'        => 0,
            'update'        => 0,
            'delete'        => 0,
        ),
        
        // 管理角色
        'admin.role' => array(
        	'list'         => 0,
        	'create'       => 0,
        	'update'       => 0,
        	'delete'       => 0,
        ),
    		
    	'account' => array(
        	'register'        => 4,
        	'login'           => 4,
        	'logout'          => 4,
        	'forgetpassword'  => 4,
        	'resetpassword'   => 4,
        	'profile'         => 3,
        	'center'          => 3,
    		'setpassword'     => 3,
    	),
			
		'oauth.weixin' => array(
			'login'		=>	4,
			'callback'	=>	4
		),
	),
);
