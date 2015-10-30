<?php
namespace module\wx\hook;

use core\IHook;

/**
 * 微信粉丝取消关注处理
 * 
 * @package     module.points.hook
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class UnsubscribeHook implements IHook {	
	
	/**
	 * 
	 */
	public function execute($params = array()) {
		$connect = \WXConnect::getInstance();
		$openid = $connect->exchange->FromUserName;
		
		$fansObj = new \module\wx\model\FansModel();
		$fansObj->setPkv($openid);
		//存在用户关注信息则设为未关注
		if ($fansObj->load()) {
			$fansObj->alterField(array('issubscribe' => 0));
		}
	}
}
