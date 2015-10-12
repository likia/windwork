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
 * 统计模型
 *
 *
 *
 * @package     module.wx.model
 * @copyright   © 2010-2015 恒辉科技版权所有
 * @author      恒辉科技 <cmpan@qq.com>
 */
class StatsModel extends \core\mvc\Model {
	/**
	 * 模型对应的数据表
	 * @var string
	 */
	protected $table = 'wx_stats';

	/**
	 * @param number $days 过去几天到下一天的0点0分0秒
	 * @param timestamp $toDay 默认是现在的晚上0点0分0秒，也就是下一天的0点0分0秒
	 */
	public function countLastPVByDays($days = 7, $toDay = NULL){
		if (is_null($toDay)) {
			$toDay = mktime (0, 0, 0, date("m"), date("d")+1, date("Y"));
		}
		$options = array(
			'where' => array(
				array('dateline', mktime (0, 0, 0, date("m"), date("d")-$days+1, date("Y")), '>='),
				array('dateline', $toDay, '<='),
			)
		);

		return $this->count($options);
	}

	/**
	 * @param number $days 过去几天到下一天的0点0分0秒
	 * @param timestamp $toDay 默认是现在的晚上0点0分0秒，也就是下一天的0点0分0秒
	 */
	public function countLastUVByDays($days = 7, $toDay = NULL){
		if (is_null($toDay)) {
			$toDay = mktime (0, 0, 0, date("m"), date("d")+1, date("Y"));
		}
		$options = array(
			'where' => array(
				array('dateline', mktime (0, 0, 0, date("m"), date("d")-$days+1, date("Y")), '>='),
				array('dateline', $toDay, '<='),
			),
		);

		return $this->count($options, 'distinct ip1, ip2, ip3, ip4');
	}
}
