<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\wx\model;

/**
 * 关注者模型
 *
 * 
 * 
 * @package     module.wx.model
 * @copyright   © 2010-2015 恒辉科技版权所有
 * @author      恒辉科技 <cmpan@qq.com>
 */
class MsgEventSubscribeModel extends \core\mvc\Model {
    /**
     * 模型对应的数据表
     * @var string
     */
    protected $table = 'msg_event_subscribe';
    
    /**
     * 保存关注信息
     * @param array $follow
     * @return boolean
     */
    public static function saveFollow($follow) {
    	$thisObj = new self();
    	$thisObj->fromArray($follow);
    	if(false === $thisObj->create()) {
    		throw new \core\mvc\Exception($thisObj->getLastErr());
    	}
    }
    
    /**
     * 是否已经关注过，true则关注过，false则未关注过
     * @param string $openId
     */
    public static function isAlreadyFollowByOpenId($openId){
    	$thisObj = new self();
    	$whArr = array(
    		array('openid', $openId)
    	);
    	if ($thisObj->loadBy($whArr)) {
    		return TRUE;
    	}
    	return FALSE;
    }
}
