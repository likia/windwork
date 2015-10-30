<?php
/**
 * 微信交互接口钩子
 * 
 * 设置方式1：钩子类名或钩子类的实例,如：'\\user\\hook\\Acl', new \module\user\hook\Acl()
 * 设置方式2：钩子类名或钩子类的实例+数组参数,如：array('\\user\\hook\\Acl', array($param1, $param2, ....)), array(new \module\user\hook\Acl(), array($param1, $param2, ....))
 */
return array(
	// 开始微信消息接口交互
	'wx_start_connect' => array(
		
	),
	// 处理微信交互消息前
	'wx_handler_connect' => array(
		
	),
	// 微信自动推送地理位置消息事件
	'wx_on_location' => array(
		
	),
	// 关注事件（包括未关注时扫描二维码）
	'wx_on_subscribe' => array(
		'\module\wx\hook\SubscribeHook',
	),
	// 取消关注事件
	'wx_on_unsubscribe' => array(
		'\module\wx\hook\UnsubscribeHook',
	),
	// 扫描带参数的二维码事件
	'wx_on_scan' => array(
		
	),
	// 打开链接事件
	'wx_on_view' => array(
		
	),
	// 点击关键词事件
	'wx_on_keyword' => array(
		
	),
	// 无关键词回复
	'wx_no_keyword_reply' => array(
		
	),
	// 关键词消息回复
	'wx_keyword_reply' => array(
		
	),
	// 粉丝发送图片消息回复
	'wx_image_reply' => array(
		
	),
	// 粉丝发送语音消息回复
	'wx_voice_reply' => array(
		
	),
	// 粉丝发送视频消息回复
	'wx_video_reply' => array(
		
	),
	// 粉丝发送小视频消息回复
	'wx_shortvideo_reply' => array(
		
	),
	// 粉丝发送地理位置消息回复
	'wx_location_reply' => array(
		
	),
	// 粉丝发送URL消息回复
	'wx_link_reply' => array(
		
	),
);