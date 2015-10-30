<?php
/**
 * Windwork Controller
 *
 * @link        http://www.windwork.org
 * @copyright   Copyright (c) 2008-2014 Windwork Team. (http://www.windwork.org)
 */
namespace module\user\controller\oauth;

use core\util\wx\Api;
/**
 * 微信自动登录控制器
 *
 * 
 * 
 * @package     module.user.controller.oauth
 * @copyright   Copyright (c) 2008-2014 Windwork Team.
 * @author      windwork.org <cmm@windwork.org>
 */
class WeixinController extends \module\system\controller\base\SmartController {
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 
     */
    public function loginAction() {
    	$code = $this->request->getRequest('code');
    	$forward = $this->request->getRequest('forward');

    	if (!$code) {
	    	//$forward = $this->request->getRequestUri();
    		$forward = paramDecode($forward);
	    	$forward = \core\util\Encoder::encode($forward);
	    	
	    	$redirectUri = urlencode(url("user.oauth.weixin.login/buid:{$this->wx->buid}/forward:{$forward}", 1));
	    	//logging('debug', $redirectUri);
	    	header("Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->wx->wxappid}&redirect_uri={$redirectUri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect");
	    	return;
    	}
    	

    	$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->wx->wxappid}&secret={$this->wx->wxsecret}&code={$code}&grant_type=authorization_code";
    	$client = new \core\util\Client();
    	$r = $client->get($url);

    	$r = (array)json_decode($r);
    	if ($r['errcode'] > 0) {
    		throw new \core\Exception(\core\util\wx\ResponseCode::getMessage($r['errcode']));
    	}
    	$openId = $r['openid'];

    	$userObj = new \module\user\model\UserModel();
    	$fansObj = new \module\wx\model\FansModel();
    	$wasSubscribe = $fansObj->setPkv($openId)->load();
    	
    	$buid = $this->biz->buid;
    	
    	if (!$wasSubscribe || $fansObj->issubscribe == 0) {
	    	// 获取会员信息
	    	$accessToken = Api::getAccessTokenByAppIdSecret($this->wx->wxappid, $this->wx->wxsecret);
	    	$userInfo = Api::getBasicUserInfoByAccessTokenOpenId($accessToken, $openId);
	    	
	    	if($userInfo['subscribe'] == 0) {
	    		$this->response->sendRedirect('wx.index.subscribe/buid:'.$buid);
	    		return;
	    	} else {
	    		// 该会员已经关注
	    		if ($wasSubscribe) {
	    			// 存在关注信息
	    			$fansObj->issubscribe == 1;
	    			$fansObj->save();
	    		} else {
	    			// 不存在关注信息
	    		    $userObj->wxCreateByInfo($userInfo);
	    			$fansInfo = array(
    			 		'openid'      => $openId,
    			 		'buid'        => $buid,
	    			 	'uid'         => $userObj->uid,
    			 		'issubscribe' => 1,
    			 		'dateline'    => time(),
	    			);
	    			
	    			$pointsSettingObj = new \module\points\model\PointsSettingModel();
	    			if($pointsSettingObj->setPkv($buid)->load()) {
	    				$fansInfo['total_points'] = $pointsSettingObj->subscribe_point;
	    			}
	    			
	    			$fansObj->fromArray($fansInfo);
	    		}
	    	}
    	}
    	
    	$userObj->loadByOpenId($openId);
    	$userObj->setLoginSession();
    	
    	$forward = \core\util\Encoder::decode($forward);
    	$this->response->sendRedirect($forward);
    }
    
    /**
     * 
     */
    public function callbackAction() {

    }
}
