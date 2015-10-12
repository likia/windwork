<?php

namespace core\util;

class Input {
	
	/**
	 * 验证检查表单重复提交
	 * 
	 * @return bool false：重复提交，验证不通过；true：验证通过
	 */
	public static function checkRePost() {
		$rePostSessionKey = '^form.post.hash';
		isset($_SESSION[$rePostSessionKey]) || $_SESSION[$rePostSessionKey] = array();
		
		$hash = sprintf('%x', abs(crc32(serialize(array_merge($_GET, $_POST, $_FILES)))));
		$uriHash = sprintf('%x', abs(crc32(\core\App::getInstance()->getRequest()->getRequestUri())));
		
		if(isset($_SESSION[$rePostSessionKey][$uriHash]) && $_SESSION[$rePostSessionKey][$uriHash] == $hash) {
			return false;
		}
		
		$_SESSION[$rePostSessionKey][$uriHash] = $hash;
		
		if(count($_SESSION[$rePostSessionKey]) > 10) {
			array_shift($_SESSION[$rePostSessionKey]);
		}
		
		return true;
	}
}

