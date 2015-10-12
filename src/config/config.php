<?php
/**
 * 配置文件, 
 * 
 * 非专业人员请不要随意编辑该文件
 */
return array (
	'env'                     => 'dev',              // 系统运行环境模式：dev开发，testing测试，production正式上线产品
	'enable_hook'             => 1,                  // 启用Hook
	'url_rewrite'             => 1,                  // 启用URLRewrite
	
    // 默认请求处理应用
	'default_mod'             => 'system',	         // 默认应用
	'default_ctl'             => 'default',          // 默认控制器
	'default_act'             => 'index',            // 默认action
    
	/* 数据库设置 */

	'db_host'                 => '192.168.1.8',      // 数据库服务器主机域名或ip
	'db_host'                 => '127.0.0.1',        // 本机测试
	'db_port'           	  => '3306',             // 数据库服务器端口
	'db_name'                 => 'windworkdb',           // 数据库名
	'db_user'                 => 'root',             // 数据库连接用户名
	'db_pass'                 => '123456',           // 数据库连接密码
	'db_table_prefix'         => 'wk_',              // 表前缀
	'db_debug'                => 0,
	
	'log_dir'                 => 'data/log',         // 新浪云使用 saekv://data/log或saemc://data/cache
	'log_wrapper_class'       => '',
	'log_enabled'             => 1,                  // 启用后台管理日志
	
	'locale'                  => 'zh_CN',            // 系统默认本地化语言
    'gzcompress'              => 0,                  // 启用压缩开关,当web服务器不支持文本内容压缩的时候建议启用

    'cache_enabled'           => 1,                  // 是否启用缓存
    'cache_dir'               => 'data/cache',       // 缓存文件夹，如果使用缓存服务器，设置通过wrapper访问，如：radius://localhost:1812/data/cache
    'cache_wrapper_class'     => '',
    'cache_expire'            => 3600,               // 缓存更新周期(默认：3600s)
	'cache_compress'          => 0,                  // 是否启用缓存内容压缩后存贮
	
	// 视图
	'tpl_compiled_force'       => 1,                 // 强制编译模板开关
	'tpl_compiled_merge'       => 1,                 // 将编模板合并成一个文件
	'tpl_compiled_check'       => 1,                 // 检查编译模板开关	
	'tpl_compiled_dir'         => 'data/template',   // 编译后模板存放（相对于根目录）的目录；新浪云使用 saekv://data/template
	'tpl_wrapper_class'        => '',

    // 组建工厂实现类的选择
    'factory_cache'           => 'file',
    'factory_logger'          => 'file',
	'factory_db'              => 'PDOMySQL',          // PDOMySQL|MySQL
	'factory_session'         => 'standard',          // session处理方式，可选择：standard|apc|memcache|DB
    'factory_mq'              => '',
	'factory_sms'             => 'PicaSMS', // 发送短信的组件                  
    'factory_crypt'           => 'AzDG',                  
    'factory_image'           => 'GD',                // 图片处理接口
    'factory_captcha'         => 'GD',                // 验证码
    'factory_mailer'          => 'SMTP',              // Mail|SMTP 邮件发送接口，linux下运行有邮件服务器使用Mail，否则使用SMTP
        

    //*
    // 内存缓存服务器设置
    'mm_prefix'               => 'wkmm_',         //
    'mm_redis_host'           => '',              //
    'mm_redis_port'           => 6379,            //
    'mm_redis_pconnect'       => 1,               //
    'mm_redis_timeout'        => 0,               //
    'mm_memcache_host'        => '',              //
    'mm_memcache_port'        => 11211,           //
    'mm_memcache_pconnect'    => 1,               //
    'mm_memcache_timeout'     => 1,               //
    
    //*/
    'sms' => array(			
		'regcode' => 'ZXHD-CRM-0100-ITSSIN',	// 登录名（易加客）
	// 	'regcode' => 'ZXHD-CRM-0100-MYVTUJ',	// 登录名（恒辉科技）		
		'pwd' => '9135e8125a196661bb0c82e59ef5bea6',	//密码（md5后的）易加客
	// 	'pwd' => '78cb9aaa175dce590283d3460960c4b5',	//密码（md5后的）恒辉科技		
		'phone' => '', // 接收短信的手机号码，为了保证参数的顺序，不要去掉		
		'content' => '', // 短信的内容，为了保证参数的顺序，不要去掉		
		'extnum' => '', // 扩展子号码（最多2位）		
		'level' => '1', // 发送优先级别（1-5级别递增）		
		'schtime' => 'null', // 定时发送时间设置	
		// 是否要状态报告（0：需要，1：不需要）
		// 如果需要发送成功后会返回一个大于0的整形值（如果是单发短信会返回：（发送的手机号：系统生成的唯一值）例（13911112222:123456789），如果是群发会返回一组值：格式：（13911112222:123456789;13822221111:21345678）
		'reportflag' => '1',		
		'url' => '',  // Wapush短信的地址		
		'smstype' => '0', // 信息类型（短信：0； wapush：2；长短信：4）		
		'key' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', // 保留，32个a
    ),
    
	// 
	'auth_key'                => 'hi@danpin.club',   // 自动登录加密密钥(系统安装时随机生成)
	'auth_name'               => 'wk_auth',          // 自动登录的cookie下标
	
	// 附件设置
	'storage_dir'             => '../storage',          // 附件存贮文件夹（相对于src文件夹，或使用完整路径），支持wrapper，如：saekv://或ftp://name:pw@yousite/
	'storage_site_url'        => 'storage',          // 附件目录url，格式：http://www.windwork.org/storage（后面不要带'/'，如果上传附件网站跟站点不是同一个站时设置）
    'storage_size_limit'      => '2048',             // (K)文件上传大小限制
    
    // URL
    'static_url'             => 'static',            // http://www.windwork.org/static/

	'static_site_url'         => '',                 // static文件夹中的附件使用的独立网址，如：http://static.windwork.org	
	'send_status'             => 1,                  // 发送 HTTP Header状态码开关, 发送状态码对搜索引擎友好，但有些服务器一旦发送状态码则会使用自定义的页面

	'install_lock'            => 'data/install.lock',
	'super_uid'                => 1,
);
