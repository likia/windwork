<?php
return array(
    // 功能表
    'acts' => array(
        'admin.diymenu' => array (
            'list'   => '微信自定义菜单列表',
            'update' => '编辑微信自定义菜单',
            'delete' => '删除微信自定义菜单',
            'create' => '添加微信自定义菜单',
            'build'  => '生成微信自定义菜单列表',
        ),
    ),
    
    // 模块功能权限控制, level:0-4(权限依次为：超级管理员；管理员；编辑；会员；游客)
    'acls' => array(
        'admin.diymenu' => array (
            'list'   => 1,
            'update' => 1,
            'delete' => 1,
            'create' => 1,
            'build'  => 1,
        ),
    ),
);
