<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\wx\controller\admin;

use core\mvc\Message;

/**
 * 
 *
 * 
 * 
 * @package     module.wx.controller.admin
 * @copyright   © 2010-2015 恒辉科技版权所有
 * @author      恒辉科技 <cmpan@qq.com>
 */
class DiyMenuController extends \module\system\controller\admin\BaseController {
	/**
	 * 
	 * @var \module\wx\model\DiyMenuModel
	 */
	protected $diyMenu;
	
    public function __construct() {
        parent::__construct();
        $this->diyMenu = new \module\wx\model\DiyMenuModel();
        $menuList = $this->diyMenu->getList();
        $this->view->assign('menuList', $menuList);
    }

    /**
     * 编辑公众号自定义菜单列表
     */
    public function listAction() {
    	if ($this->request->isPost()) {
    		$sortArr = $this->request->getPost('sort');
    		foreach ($sortArr as $menuId => $sortVal) {
    			$this->diyMenu->updateDisplayorderById($sortVal, $menuId);
    		}
    		
    		Message::setOK('排序成功！');
    		
    		$menuList = $this->diyMenu->getList();
    		$this->view->assign('menuList', $menuList);
    	}
    	$this->view->render();
    }
    
    /**
     * 编辑添加公众号自定义菜单
     */
    public function createAction() {
    	if($this->request->isPost() && $this->request->checkRePost()) {
    		$data = array(
    			'parentid'      => (int)$this->request->getPost('parentid'),
    			'name'          => $this->request->getPost('name'),
    			'keyword'       => $this->request->getPost('keyword'),
    			'url'           => $this->request->getPost('url'),
    			'displayorder'  => (int)$this->request->getPost('displayorder')
    		);
    		$this->diyMenu->fromArray($data);
    		if(false !== $this->diyMenu->create()) {
    			Message::setOK('添加菜单成功！');
    		} else {
    			Message::setErr($this->diyMenu->getErrs());
    		}
    		
    		if ($this->request->isAjaxRequest()) {
    			$this->showMessage();
    			return;
    		}
    	}
    	
    	$this->view->render();
    	
    	if ($this->request->isAjaxRequest()) {
    		$this->showMessage(ob_get_clean());
    	}
    }

    /**
     * 编辑自定义菜单
     */
    public function updateAction($id) {
    	if(!$this->diyMenu->loadById($id)) {
    		$this->err404();
    		return;
    	}
    	
    	if($this->request->isPost() && $this->request->checkRePost()) {
    		$data = array(
    			'parentid'      => (int)$this->request->getPost('parentid'),
    			'name'          => $this->request->getPost('name'),
    			'keyword'       => $this->request->getPost('keyword'),
    			'url'           => $this->request->getPost('url'),
    			'displayorder'  => (int)$this->request->getPost('displayorder')
    		);
    		$this->diyMenu->fromArray($data);
	    	if(false !== $this->diyMenu->updateById($id)) {
	    		Message::setOK('编辑菜单成功！');
	    	} else {
	    		Message::setErr($this->diyMenu->getErrs());
	    	}
	    }
    	    	
	    $this->diyMenu->loadById($id);
	    
	    $this->view->assign('menu', $this->diyMenu->toArray());
    	$this->view->render();

    	if ($this->request->isAjaxRequest()) {
    		$this->showMessage(ob_get_clean());
    	}
    }

    /**
     * 编辑删除公众号自定义菜单
     */
    public function deleteAction($id) {
    	if(false !== $this->diyMenu->deleteById($id)) {
    		Message::setOK('删除菜单成功！');
    	} else {
    		Message::setErr($this->diyMenu->getErrs());
    	}
    	
    	$this->showMessage();
    }

    /**
     * 编辑生成公众号自定义菜单
     */
    public function buildAction() {
    	$appId = \core\Config::get('wx_app_id');
    	$appSecret = \core\Config::get('wx_app_secret');
    	
    	$accessToken = \core\util\wx\Api::getAccessTokenByAppIdSecret($appId, $appSecret);
    	if(false !== $this->diyMenu->buildWXMenuByAccessToken($accessToken)) {
    		Message::setOK('恭喜你，生成微信自定义菜单成功！');
    	} else {
    		Message::setErr($this->diyMenu->getErrs());
    	}
    	
    	$this->showMessage();
    }
}
	

