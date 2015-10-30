<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\util\wx;

use core\Factory;
/**
 * 微信API接口调用类
 *
 * @package     core.util.wx
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */ 
class Api  {
	/**
	 * 获取公众号  access_token
	 * 
	 * @param string $appId
	 * @param string $secret
	 * @return boolean|string
	 */
	public static function getAccessTokenByAppIdSecret($appId, $secret) {
		$cacheKey = "wx/access_token/{$appId}^{$secret}";
		if(false == ($token = Factory::cache()->read($cacheKey))) {
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appId}&secret={$secret}";
			
			$client = new \core\util\Client();
			$rsp = $client->get($url);
			if (!$rsp) {
				throw new \core\Exception('网络不通到微信服务器！');
			}
			 
			$rsp = (array)json_decode($rsp);
			if (isset($rsp['errcode'])) {
				throw new \core\Exception(\core\util\wx\ResponseCode::getMessage($rsp['errcode']));
			}
		
			$token = $rsp['access_token'];
			Factory::cache()->write($cacheKey, $token, 3600);
		};
		
		return $token;
	}
	
	/**
	 * 给粉丝推送客服图文消息
	 * @param array $articles
	 * @param string $toOpenId
	 * @param string $accessToken
	 * @return string
	 */
	public static function pushCustomerNews($articles, $toOpenId, $accessToken) {
		$articles = (array)$articles;	
		if (!empty($articles['title'])) {
			$articles = array($articles);
		}
		
		$articleStr = '';
		foreach ($articles as $article) {
			$article = (array)$article;
			foreach ($article as $key => $val) {
				$article[$key] = preg_replace("/([\\b\\t\\n\\r\\f\"\\'\\/])/", "\\\\\\1", $val);
			}
			
			$articleStr .= '{
				"title":"'.$article['title'].'",
				"description":"'.$article['description'].'",
				"url":"'.$article['url'].'",
				"picurl":"'.$article['picurl'].'"
			},';			
		}
		$articleStr = rtrim($articleStr, ',');
		
		$msg = '{
		    "touser":"'.$toOpenId.'",
		    "msgtype":"news",
		    "news":{
		        "articles": ['.$articleStr.']
		    }
		}';
		
    	$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$accessToken}";
    	$client = new \core\util\Client();
    	$r = $client->post($url, $msg);
    	
    	return $r;
	}
	
	/**
	 * 根据access_token和open id 获取用户基本信息
	 * @param string $accessToken 基础支持的access_token
	 * @param string $openId
	 * @throws \core\Exception
	 */
	public static function getBasicUserInfoByAccessTokenOpenId($accessToken, $openId) {
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$accessToken}&openid={$openId}&lang=zh_CN";
		$client = new \core\util\Client();
		$json = $client->get($url);
		if(!$json) {
			throw new \core\Exception('无法连接到微信服务器！');
		}
		$userInfo = (array)json_decode($json);
		if ($userInfo['errcode']) {
			$errmsg = \core\util\wx\ResponseCode::getMessage($userInfo['errcode']);
			throw new \core\Exception($errmsg);
		}
		
		return $userInfo;
	}
	
	/**
	 * 获取二维码ticket
	 * @param string $accessToken
	 * @param int $sceneId 场景值ID，1-100000之间
	 * @param number $expireSeconds 过期时间，如果为0则是永久的ticket，大于0则为ticket的过期时间，默认为1800
	 * @param string $scenceStr 场景值ID(String类型)，仅永久二维码有效，如果不为null则使用该值不使用$sceneId
	 */
	public static function getTicketInfoByAccessTokenSceneId($accessToken, $sceneId, $expireSeconds = 1800, $scenceStr = null){
		$sceneId = intval($sceneId);
		$expireSeconds = intval($expireSeconds);
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$accessToken}";
		if ($expireSeconds > 1800 || $expireSeconds < 0){
			throw new \core\Exception('二维码有效时间只能在0-1800之间');
		}
		
		// 永久的二维码链接
		if ($expireSeconds == 0) {
			if (!is_null($scenceStr)) {
				if (!\core\util\Validator::isSafeString($scenceStr)) {
					throw new \core\Exception('场景值ID（str）值不安全');
				}else{
					$data = array(
						'action_name' => 'QR_LIMIT_STR_SCENE',
						'action_info' => array(
							'scene' => array('scene_str' => $scenceStr)
						)
					);
				}
			} else {
				if ($sceneId > 100000 || $sceneId < 0){
					throw new \core\Exception('场景值ID只能在0-100000之间');
				}else{
					$data = array(
						'action_name' => 'QR_LIMIT_SCENE',
						'action_info' => array(
							'scene' => array('scene_id' => $sceneId)
						)
					);
				}
			}
		// 临时的二维码链接
		} else {
			$data = array(
				'expire_seconds' => $expireSeconds,
				'action_name' => 'QR_LIMIT_SCENE',
				'action_info' => array(
					'scene' => array('scene_id' => $sceneId),
				)
			);
		}
		
		$client = new \core\util\Client();
		$rsp = $client->post($url, json_encode($data));
		if (!$rsp) {
			throw new \core\Exception('网络不通到微信服务器！');
		}
		
		$rsp = (array)json_decode($rsp);
		if ($rsp['errcode']) {
			throw new \core\Exception(\core\util\wx\ResponseCode::getMessage($rsp['errcode']));
		}
		
		return $rsp;
	}
	
	/**
	 * 根据ticket获取二维码链接
	 * @param string $ticket
	 * @return string
	 */
	public static function getQRCodeUrlByTicket($ticket){
		if (empty($ticket)) {
			return null;
		}
		return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticket);
	}
	
	/**
	 * 根据accessToken和sceneId等获取二维码链接
	 * @param string $accessToken
	 * @param int $sceneId 场景值ID，1-100000之间
	 * @param number $expireSeconds 过期时间，如果为0则是永久的ticket，大于0则为ticket的过期时间，默认为1800
	 * @param string $scenceStr 场景值ID(String类型)，如果不为null则使用该值不使用$sceneId
	 */
	public static function getQRCodeUrlByAccessTokenSceneId($accessToken, $sceneId, $expireSeconds = 1800, $scenceStr = null){
		$ticketInfo = static::getTicketInfoByAccessTokenSceneId($accessToken, $sceneId, $expireSeconds, $scenceStr);
		if(empty($ticketInfo)){
			return null;
		}
		return static::getQRCodeUrlByTicket($ticketInfo['ticket']);
	}
}

