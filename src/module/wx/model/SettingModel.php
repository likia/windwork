<?php
/**
 * Windwork model
 *
 * @link        http://www.henghuiit.com
 * @copyright   © 2010-2015 恒辉科技版权所有
 */
namespace module\wx\model;

/**
 * 微信设置模型
 *
 * 
 * 
 * @package     module.wx.model
 * @copyright   © 2010-2015 恒辉科技版权所有
 * @author      恒辉科技 <cmpan@qq.com>
 */
class SettingModel extends \core\mvc\Model {
    /**
     * 模型对应的数据表
     * @var string
     */
    protected $table = 'wx_setting';
        
    /**
     * (non-PHPdoc)
     * @see \core\mvc\Model::load()
     */
    public function load(){
    	if(!parent::load()) {
    		$this->create();
    		parent::load();
    	}
    	
    	return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see \core\mvc\Model::create()
     */
    public function create() {
    	$this->token = \core\Common::guid(16);
    	$this->dateline = time();
    	
    	return parent::create();
    }
    
    /**
     * (non-PHPdoc)
     * @see \core\mvc\Model::update()
     */
    public function update() {
    	$this->addLockFields(array('token', 'buid', 'dateline'));
    	
    	if (!$this->name) {
    		$this->setErr('请填写公众号名称！');
    		return false;
    	}
    	
    	if (!$this->wxsrcid) {
    		$this->setErr('请填写微信公众号原始ID！');
    		return false;
    	}
    	return parent::update();
    }
    
}
