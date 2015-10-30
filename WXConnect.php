<?php

/**
 * 处理微信API请求
 */
use core\Hook;

define('IS_IN', true);

require_once 'src/core/App.php';
require_once SRC_PATH.'core/util/wx/Exchange.php';

try {
	$app = \core\App::getInstance();
	$connect = WXConnect::getInstance();
	$connect->handler();	
} catch (\core\Exception $e) {
	\core\Common::exceptionHandler($e);
}

/**
 * 微信连接接口服务
 * 
 * 微信发送的信息通过WXConnect::$exchange获取
 *
 */
class WXConnect {
	/**
	 * 当前方法仅仅处理一次请求
	 * @var \core\util\wx\Exchange
	 */
	public $exchange;
	
	/**
	 * 微信账号信息
	 * @var \module\wx\model\SettingModel
	 */
	public $wx;
	
	public static $instance = null;
	
	/**
	 * @return \WXConnect
	 */
	public static function getInstance() {
		if (null === static::$instance) {
			static::$instance = new static();
		}
		
		return static::$instance;
	}
		
	protected function __construct() {
	    // 开始微信消息接口
	    Hook::call('wx_start_connect');
	    
		$this->wx = new \module\wx\model\SettingModel();
		
		$buid = (int)$_GET['buid'];
		if(!$buid || !$this->wx->setPkv($buid)->load()) {
			throw new \core\Exception("商家不存在！(buid:{$buid})");
		}
		
		$this->exchange = new \core\util\wx\Exchange($this->wx->token);

		logging('debug', $this->exchange);
	}
	
	public function handler() {
	    // 微信接口连接处理
	    Hook::$hooks = array_merge(Hook::$hooks, include SRC_PATH . 'config/hooks.wxapi.php');
		
	    // 处理微信交互消息前
	    Hook::call('wx_handler_connect');
	    
	    // -------------------
	    // 事件消息
		if($this->exchange->MsgType == 'event') {
			// 微信自动推送地理位置消息
			if($this->exchange->Event == 'LOCATION') {
				// 记录用户的地理位置
				$userObj = new \module\user\model\UserModel();			
				$posData = array(
					'lat' => $this->exchange->Latitude,
					'lng' => $this->exchange->Longitude,
					'setlocationtime' => time(),
				);
				
				$userObj->updateBy($posData, array('openid', $this->exchange->FromUserName));
				
				Hook::call('wx_on_location');
				return;
			} 
		    // 关注事件（包括未关注时扫描二维码）
		    else if($this->exchange->Event == 'subscribe') {
		        // 关注时处理
		        Hook::call('wx_on_subscribe');
		    	return;
		    }
		    // 取消关注事件
		    else if($this->exchange->Event == 'unsubscribe') {
		        // 关注时处理
		        Hook::call('wx_on_unsubscribe');
		    	return;
		    }
		    // 扫描带参数的二维码事件
		    else if($this->exchange->Event == 'SCAN') {
		        Hook::call('wx_on_scan');
		    	return;
		    }
		    // 打开链接事件
		    else if($this->exchange->Event == 'VIEW') {
		        Hook::call('wx_on_view');
		    	return;
		    }
		    // 点击关键词事件
		    else if($this->exchange->Event == 'EVENTKEY') {/*
		    	// 分析关键词
	    		$keyword = $this->exchange->EventKey;
	    		$keyword = trim($keyword);
	
	    		$msgObj = new \module\wx\model\MsgKeywordModel();
	    		$msgObj->fromArray($msg);
	    		$msgObj->keyword = $keyword;
	
	    		// 保存关键词消息查询
	    		$msgObj->create();
	    		*/
		        Hook::call('wx_on_keyword');
		        
		        // 从关键词库回复
		        if(!$this->exchange->isResponsed) {
		        	// TODO 从关键词库回复信息
		        }

		        // 未响应则回复默认消息（无关键词回复）
		        if(!$this->exchange->isResponsed) {
		        	// 没有符合条件的回复信息时，回复内容
		        	Hook::call('wx_no_keyword_reply');
		        }
		        
		    	return;
		    }
		} 

		// 普通消息
		else {
			// 关键词消息回复
			if ($this->exchange->MsgType == 'text') {
				/*
				 // 分析关键词
				$keyword = $this->exchange->MsgType == 'text' ? $this->exchange->Content : $this->exchange->EventKey;
				$keyword = trim($keyword);
			
				$msgObj = new \module\wx\model\MsgKeywordModel();
				$msgObj->fromArray($msg);
				$msgObj->keyword = $keyword;
			
				// 保存关键词消息查询
				$msgObj->create();
				*/
				Hook::call('wx_keyword_reply');
			
		        // 从关键词库回复
		        if(!$this->exchange->isResponsed) {
		        	// TODO 从关键词库回复信息
		        }
			}
			// 粉丝发送图片消息回复
			else if ($this->exchange->MsgType == 'image') {
				Hook::call('wx_image_reply');
			}
			// 粉丝发送语音消息回复
			else if ($this->exchange->MsgType == 'voice') {
				Hook::call('wx_voice_reply');
			}
			// 粉丝发送视频消息回复
			else if ($this->exchange->MsgType == 'video') {
				Hook::call('wx_video_reply');
			}
			// 粉丝发送小视频消息回复
			else if ($this->exchange->MsgType == 'shortvideo') {
				Hook::call('wx_shortvideo_reply');
			}
			// 粉丝发送地理位置消息回复
			else if ($this->exchange->MsgType == 'location') {
				Hook::call('wx_location_reply');
			}
			// 粉丝发送URL消息回复
			else if ($this->exchange->MsgType == 'link') {
				Hook::call('wx_link_reply');
			}
			
			// 未响应则回复默认消息（无关键词回复）
			if(!$this->exchange->isResponsed) {
				// 没有符合条件的回复信息时，回复内容
				Hook::call('wx_no_keyword_reply');
			}
		}
	}
}