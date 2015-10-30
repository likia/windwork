<?php
/**
 * 
 */
return array(
    // 设置方式1：钩子类名或钩子类的实例,如：'\\user\\hook\\Acl', new \module\user\hook\Acl()
	// 设置方式2：钩子类名或钩子类的实例+数组参数,如：array('\\user\\hook\\Acl', array($param1, $param2, ....)), array(new \module\user\hook\Acl(), array($param1, $param2, ....))
	'start_new_app' => array(
		
	),
	
	// 创建App单例后触发的钩子，只执行一次；
	'end_new_app' => array(
		
	),
	
	// 加载完系统配置后触发的钩子，目的是增加修改系统配置信息
	// 只在创建App单例时执行一次，框架仅初始化了request、response、自动加载、默认异常处理，其他库不可用；
	'start_init_runtime' => array(
		'\module\system\hook\InitOption', // 读取数据库保存的配置信息
	),
		
	// 初始化控制器实例前触发的钩子
	'start_new_controller' => array(
		'\module\user\hook\AuthHook', //权限控制
	),
		
	// 初始化控制器实例后触发的钩子
	'end_new_controller' => array(
		
	),
		
	// 执行action前触发的钩子
	'start_action' => array(		
		//'\module\system\hook\BannedIPHook', // IP 禁止
		//'\module\system\hook\PauseServiceHook', // 系统暂停服务信息，在后台设置
	),
		
	// 执行action后触发的钩子
	'end_action' => array(
		//'\module\system\hook\AdminCPLogHook', // 后台操作日志
	),
		
	// 内容输出前触发的钩子，可对输出内容进行处理过滤
	'start_output' => array(),
		
	// 程序执行完后触发的钩
	'end_app' => array(),
);