<?php

/**
 * 处理微信API请求
 */
use core\Config;

define('IS_IN', true);

require_once 'src/core/App.php';
require_once SRC_PATH.'core/util/wx/Exchange.php';

try {
	$app = \core\App::getInstance();
	$connect = new WXConnect();
	$connect->handler();	
} catch (\core\Exception $e) {
	\core\Common::exceptionHandler($e);
}

class WXConnect {
	/**
	 * 
	 * @var \core\util\wx\Exchange
	 */
	private $exchange;
	
	public function __construct() {
		$token = Config::get('wx_token');
		$this->exchange = new \core\util\wx\Exchange($token);	    
	}
	
	public function handler() {
		$openId = $this->exchange->FromUserName;
		$devId  = $this->exchange->ToUserName;
	    $msg = array(
	    	'msgid'    => $this->exchange->MsgId,
	    	'msgtype'  => $this->exchange->MsgType,
	    	'openid'   => $openId,
	    	'devid'    => $devId,
	    	'dateline' => time(),
	    );
		
	    // 关注
	    if($this->exchange->MsgType == 'event' && $this->exchange->Event == 'subscribe') {
	    	// 未关注过，添加星值
	    	if (!\module\wx\model\MsgEventSubscribeModel::isAlreadyFollowByOpenId($openId)) {
	    		require_once SRC_PATH.'compat.php';
	    		$eventKey = empty($this->exchange->EventKey) ? "" : $this->exchange->EventKey;
	    		// 如果是带场景的二维码关注
	    		if (!empty($eventKey) && false !== stripos($eventKey, 'qrscene')) {
	    			$scenceId = intval(ltrim(strstr($this->exchange->EventKey, '_'), '_'));
	    			// 0-100000
	    			if ($scenceId > 0 && $scenceId < 100000) {
		    			// 修理厂的star值增加1
	    			}
	    		}
	    	}
	    	// 保存关注信息
	    	\module\wx\model\MsgEventSubscribeModel::saveFollow($msg);
	    		    	
	    	// 响应关注时提示信息
	    	$this->followReply();
	    	return;
	    }
	    
	    // 关键词回复
	    else if (($this->exchange->MsgType == 'event' && $this->exchange->Event == 'CLICK') || $this->exchange->MsgType == 'text') {
	    	// 分析关键词
    		$keyword = $this->exchange->MsgType == 'text' ? $this->exchange->Content : $this->exchange->EventKey;
    		$keyword = trim($keyword);

    		$msgObj = new \module\wx\model\MsgKeywordModel();
    		$msgObj->fromArray($msg);
    		$msgObj->keyword = $keyword;

    		// 保存关键词
    		$msgObj->create();
    		
    		if ($keyword == '掌上车宝') {
		    	// 签到提示信息    	
		    	$articles = array(
		    		array(
		    		    'Title'       => Config::get('site_name'),
		    		    'Description' => Config::get('site_description'),
		    		    'PicUrl'      => '',
		    		    'Url'         => 'http://car.henghuiit.com/',
		    		),
		    	);
		    	$this->exchange->responseArticles($articles);
    			return;
    		}

    		if ($keyword == '报修') {
		    	// 签到提示信息    	
		    	$articles = array(
		    		array(
		    		    'Title'       => Config::get('site_name') . ' 报修',
		    		    'Description' => '要修车？进入报修，让多家修车厂找到您并抢着帮您修好车！',
		    		    'PicUrl'      => '',
		    		    'Url'         => url("repair.issue.list", 1),
		    		),
		    	);
		    	$this->exchange->responseArticles($articles);
    			return;
    		}

    		if ($keyword == '修车地图') {
		    	// 签到提示信息    	
		    	$articles = array(
		    		array(
		    		    'Title'       => '修车地图',
		    		    'Description' => '进入查看修车地图，查找您的客户',
		    		    'PicUrl'      => '',
		    		    'Url'         => url("repair.issue.list/by:near/view:map", 1),
		    		),
		    	);
		    	$this->exchange->responseArticles($articles);
    			return;
    		}

    		if ($keyword == '修车厂大全') {
		    	// 签到提示信息    	
		    	$articles = array(
		    		array(
		    		    'Title'       => '修车厂大全',
		    		    'Description' => '进入查看修车厂大全，寻找中意的修车厂',
		    		    'PicUrl'      => '',
		    		    'Url'         => url("shop.shop.list/sort:near/view:map", 1),
		    		),
		    	);
		    	$this->exchange->responseArticles($articles);
    			return;
    		}
	    } 
	    
		// 微信自动推送地理位置消息
		if($this->exchange->MsgType == 'event' && $this->exchange->Event == 'LOCATION') {
			// 记录用户的地理位置
			$userObj = new \module\user\model\UserModel();			
			$posData = array(
				'lat' => $this->exchange->Latitude,
				'lng' => $this->exchange->Longitude,
				'setlocationtime' => time(),
			);
			
			$userObj->updateBy($posData, array('openid', $openId));
			
			return;
		}
		
		// 未响应则回复默认消息
		if(!$this->exchange->isResponsed) {
			$this->exchange->responseText('对不起，你的问题太难了，我实在答不上来！');
		}
	}
	
	/**
	 * 关注时回复
	 */
	protected function followReply() {
		$text = "感谢您关注 掌上车宝！";
		$this->exchange->responseText($text);
	}
	
}