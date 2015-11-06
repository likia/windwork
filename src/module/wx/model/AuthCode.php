<?php

namespace module\wx\model;

class AuthCode {
	/**
	 * 对openid进行加密，以便通过url传送
	 * 该加密后字符串绝对不能泄露到网络
	 * @param string $openId
	 * @return string
	 */
	public static function encryptOpenid($openId) {
		$time = time();
		$authCode = \core\Factory::crypt()->encrypt($openId, md5(\core\Config::get('auth_key').$time));
		$authCode = $time . "\t" . $authCode;
		$authCode = \core\util\Encoder::encode($authCode);

		return $authCode;
	}
	
	/**
	 * 从加密文本中获取openid
	 * @param string $code
	 * @return string
	 */
	public static function decryptOpenId($code) {
		$openId = '';
		$code = \core\util\Encoder::decode($code);
		@list($time, $encrypted) = explode("\t", $code);

		// $authCode有效期为2天，除安全考虑外，让粉丝重新点击菜单以便我们能在后台给粉丝主动发消息
		if ($time + 24*3600*2 >= time()) {
			$openId = \core\Factory::crypt()->decrypt($encrypted, md5(\core\Config::get('auth_key').$time));
		}
		
		return $openId;		
	}
}

