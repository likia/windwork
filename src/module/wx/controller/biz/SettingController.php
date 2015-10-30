<?php
/**
 * Windwork Controller
 *
 * @link        http://www.henghuiit.com
 * @copyright   © 2010-2015 恒辉科技版权所有 (http://www.henghuiit.com)
 */
namespace module\wx\controller\biz;

use core\mvc\Message;
/**
 * 微信设置
 *
 * 
 * 
 * @package     {package}
 * @copyright   © 2010-2015 恒辉科技版权所有
 * @author      恒辉科技 <cmpan@qq.com>
 */
class SettingController extends \module\system\controller\base\BizController {
    public function __construct() {
        parent::__construct();
        // 如果使用视图需要初始化视图，也可以在action中初始化
        //$this->initView();
    }
    
    /**
     * 
     */
    public function bindAction() {
    	$settingObj = new \module\wx\model\SettingModel();
    	$settingObj->setPkv($_SESSION['uid']);
    	$settingObj->load();
    	
    	if ($this->request->isPost()) {
    		$settingObj->fromArray($_POST);
    		if(false !== $settingObj->update()) {
    			Message::setOK('保存成功！');
    		} else {
    			Message::setErr($settingObj->getErrs());
    		}
    		
    		if($this->request->isAjaxRequest()) {
	    		$this->showMessage();
	    		return;
    		}
    	}
    	
        $this->view->assign('settingObj', $settingObj);
        $this->view->title = '绑定公众号';
        $this->view->render();
    }
    
   
}
