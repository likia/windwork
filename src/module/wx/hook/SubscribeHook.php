<?php
namespace module\wx\hook;

use core\IHook;
use core\Factory;

/**
 * 微信关注时获得积分的处理
 * 
 * @package     module.points.hook
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class SubscribeHook implements IHook {	
	
	/**
	 * 
	 */
	public function execute($params = array()) {
		$connect = \WXConnect::getInstance();
		$exchange = $connect->exchange;
		$wx = $connect->wx;
		
		$openId = $connect->exchange->FromUserName;
		$buid = $wx->buid;
		
		// 用户是否曾经关注
		$fansObj = new \module\wx\model\FansModel();
		$fansObj->setPkv($openId);
		
		//存在用户关注信息
		if ($fansObj->load()) {
			// 已取消关注则设置为已关注
			if(!$fansObj->issubscribe) {
				$fansObj->alterField(array('issubscribe' => 1));
			}
		} else {
			// 从未关注
			
			// 获取关注积分设置
			$pointsSettingObj = new \module\points\model\PointsSettingModel();
			$pointsSettingObj->setPkv($buid);
			
			$fansInfo = array(
				'openid' => $openId,
				'buid' => $buid,
				'issubscribe' => 1,
				'dateline' => time(),
			);

			try {
				Factory::db()->beginTransaction();
				
				if($pointsSettingObj->load()) {
				    // 关注者获得积分
				    if ($pointsSettingObj->subscribe_point > 0) {
				    	$fansInfo['total_points'] = $pointsSettingObj->subscribe_point;
				    }
				    
				    // 从带参数二维码取得推荐人信息
				    if(isset($exchange->EventKey) && preg_match("/qrscene_(\\d+)/", $exchange->EventKey, $match)) {
				    	$inviteuid = $match[1];
			    		// 标记推荐人
			    		$fansInfo['inviteuid'] = $inviteuid;
			    		
					    // 直接推荐人获得积分
					    if ($inviteuid > 0 && $pointsSettingObj->first_recommend_point > 0) {
					    	$inviterObj = new \module\wx\model\FansModel();
					    	if($inviterObj->loadBy(array('uid', $inviteuid))) {
					    		$inviterObj->total_points += $pointsSettingObj->first_recommend_point;
					    		$inviterObj->update();
					    		
					    		// 间接推荐人获得积分
					    		if($inviterObj->inviteuid && $pointsSettingObj->second_recommend_point > 0) {
					    			$inviterObj2 = clone $inviterObj;
					    			if($inviterObj2->loadBy(array('uid', $inviterObj->inviteuid))) {
					    				$inviterObj2->total_points += $inviterObj2->second_recommend_point;
					    		        $inviterObj2->update();
					    			}
					    		}
					    	}
					    }
				    }
				}

				// 如果用户基本信息不存在则添加（用户通过web授权登录后就存在）
				$userObj = new \module\user\model\UserModel();
				if(!$userObj->loadBy(array('openid', $openId))) {
					$userObj->wxRegister($openId, $wx->wxappid, $wx->wxsecret);
				}

				// 用户uid
				$fansInfo['uid'] = $userObj->uid;
				
				$fansObj->fromArray($fansInfo);
				$fansObj->create();
				
				//logging('debug', var_export($fansInfo, 1));
				
				Factory::db()->commit();
			} catch (\core\Exception $e) {
				Factory::db()->rollBack();
				logging('error', $e->getMessage());
			}
		}
	}
}
