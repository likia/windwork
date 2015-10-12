<?php
/**
 * Windwork Controller
 *
 * @link        {siteurl}
 * @copyright   {copyright} ({siteurl})
 */
namespace {namespace};

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
        // 如果使用视图需要初始化视图，也可以在action中初始化
        //$this->initView();
    }
    
    /**
     * 
     */
    public function indexAction() {

        
        $this->view->render();
    }
    
    /**
     * 
     */
    public function listAction() {

        
        $this->view->render();
    }
    
    /**
     * 
     */
    public function itemAction() {
        

        $this->view->render();
    }
}
