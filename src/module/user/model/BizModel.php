<?php

namespace module\user\model;

/**
 * 商家用户（经营者）
 * @author cmpan
 *
 */
class BizModel extends UserModel {
	/**
	 * 加载商家信息
	 */
	public function load() {
		if(parent::load()) {
			if ($this->type == 'member') {
				return false;
			}
			
			return true;
		}
		
		return false;
	}
}
