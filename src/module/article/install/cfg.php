<?php

return array (	
    'acts' => array (    		
        // 管理文章
        'admin.content' => array(
            'list'          => '管理文章',
            'create'        => '添加文章',
            'update'        => '编辑文章',
            'delete'        => '删除文章',
        ),

        // 管理文章栏目
        'admin.cat' => array(
        	'list'     => '管理文章栏目',
        	'create'   => '添加文章栏目',
        	'update'   => '编辑文章栏目',
        	'delete'   => '删除文章栏目',
        ),
    	
        // 管理文章栏目
        'show' => array(
        	'list'     => '文章列表',
        	'item'     => '文章详情',
        	'search'   => '文章搜索',
        	'index'    => '文章首页',
        ),
	),
	
	'acls' => array(        
        // 栏目管理
        'admin.content' => array(
            'list'          => array(1, 0, 0, 0),
            'create'        => array(1, 0, 0, 0),
            'update'        => array(1, 0, 0, 0),
            'delete'        => array(1, 0, 0, 0),
        ),

        // 管理角色
        'admin.cat' => array(
        	'list'         => array(1, 0, 0, 0),
        	'create'       => array(1, 0, 0, 0),
        	'update'       => array(1, 0, 0, 0),
        	'delete'       => array(1, 0, 0, 0),
        ),
		
        // 管理角色
        'show' => array(
        	'index'        => array(1, 1, 1, 1),
        	'list'         => array(1, 1, 1, 1),
        	'item'         => array(1, 1, 1, 1),
        	'search'       => array(1, 1, 1, 1),
        ),
	),
);
