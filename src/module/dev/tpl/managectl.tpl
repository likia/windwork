<?php
/**
 * Windwork Controller
 *
 * @link        {siteurl}
 * @copyright   {copyright} ({siteurl})
 */
namespace {namespace};

use core\mvc\Message;

/**
 * {name}
 *
 * {desc}
 * 
 * @package     {package}
 * @copyright   {copyright}
 * @author      {author} <{email}>
 */
class {class}Controller extends {parent} {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 
     */
    public function createAction() {
    	/*
        if($this->request->isPost()) {
            if(false === $this->m->fromArray($_POST)->create()) {
                Message::setErr($this->m->getErrs());
            } else {
                Message::setOK('提示信息');
            }
            
            if ($this->request->isAjaxRequest()) {
                $this->showMessage();
                return true;
            }
        }
        */
        $this->view->render();
    }
    
    /**
     * 
     */
    public function listAction() {
        /*
        $cdt = array(
        );
        
        $totals = $this->m->count($cdt);
        $paging = new \core\util\Pagination();
        $paging->setVar($totals, 15);
        $list = $this->m->select($cdt, $paging->offset, $paging->rows);
        
        $this->view->assign('list', $list);
        $this->view->assign('pager', $paging->getPager());
        */
        $this->view->render();
    }
    
    /**
     * 
     */
    public function updateAction($id = 0) {
        
        $this->view->render();
    }
    
    /**
     * 
     */
    public function deleteAction($id = 0) {
        
    }
}