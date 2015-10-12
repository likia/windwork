<?php

return array(
    // 功能列表
    'acts' => array(
        'api' => array(
            'get'  => '显示广告',
        ),
    	
        'managead' => array(
            'create'  => '新建广告',
            'update'  => '修改广告',
            'delete'  => '删除广告',
            'list'    => '广告列表',
        ),
    	
        'manageplace' => array(
            'create'  => '新建位广告',
            'update'  => '修改位广告',
            'delete'  => '删除位广告',
            'list'    => '广告位列表',
        ),
    ),

	// 默认权限设置
    'acls' => array(        
        'api' => array(
            'get'   => '*',
        ),
        'managead' => array(
            'create'  => array(1, 0, 0, 0),
            'update'  => array(1, 0, 0, 0),
            'delete'  => array(1, 0, 0, 0),
            'list'    => array(1, 0, 0, 0),
        ),
    	
        'manageplace' => array(
            'create'  => array(1, 0, 0, 0),
            'update'  => array(1, 0, 0, 0),
            'delete'  => array(1, 0, 0, 0),
            'list'    => array(1, 0, 0, 0),
        ),
    ),
);
