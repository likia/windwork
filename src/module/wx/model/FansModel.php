<?php
/**
 * Windwork model
 *
 * @link        http://www.henghuiit.com
 * @copyright   © 2010-2015 恒辉科技版权所有
 */
namespace module\wx\model;

/**
 * 微信粉丝模型
 *
 * @package     module.wx.model
 * @copyright   © 2010-2015 恒辉科技版权所有
 * @author      恒辉科技 <cmpan@qq.com>
 */
class FansModel extends \core\mvc\Model {
    /**
     * 模型对应的数据表
     * @var string
     */
    protected $table = 'wx_fans';
    
    /**
     * 根据商家id和用户id加载关注信息
     * @param int $buid
     * @param int $uid
     * @return boolean
     */
    public function loadByBuidUid($buid, $uid) {
    	return $this->loadBy(array(array('buid', $buid), array('uid', $uid)));
    }
    
    /**
     * 
     * @param unknown $buid
     * @param number $rows
     * @return Ambigous <multitype:, multitype:\core\util\Pagination NULL number multitype: >
     */
    public function getResByBuid($buid, $rows = 10) {
    	$cdt = array(
    		'where' => array('buid', $buid),
    		'order' => 'uid DESC',
    	);
    	return $this->getListWithPager($cdt, $rows);
    }
    
    /**
     * 会员推荐粉丝列表
     * @param int $buid
     * @param int $uid
     * @param int $rows
     * @return Ambigous <multitype:, multitype:\core\util\Pagination NULL number multitype: >
     */
    public function getInviteUserRes($buid, $inviteUid, $rows = 10) {
    	$cdt = array(
    		'where' => array(
    			array('buid', $buid), 
    			array('inviteuid', $inviteUid)
    	    ),
    		'order' => 'uid DESC',
    	);
    	return $this->getListWithPager($cdt, $rows);
    }
    
}
