<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\controller\admin;

use core\Lang;
use core\mvc\Message;
use module\system\model\OptionModel;
use module\system\model\ModuleModel;

/**
 * 系统设置
 *
 * @package     module.system.controller.admin.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class OptionController extends \module\system\controller\admin\AdminBase {
	/**
	 * 
	 * @var \module\system\model\OptionModel
	 */
	private $m;
	
	public function __construct(){
		parent::__construct();
		$this->m = new OptionModel();
		$this->initView();
	}
	
	public function listAction($group = '') {
		$group || $group = 'system';
		
		if ($this->request->isPost()) {
			foreach ($_FILES as $key => $file) {
				// 系统设置文件上传只允许是图片
				if ($file['error'] || !$file['tmp_name'] || false === strpos($file['type'], 'image')) {
					continue;
				}
				
				$uploadObj = new \module\system\model\UploadModel();
				$uploadObj
				  ->setTempFile($file['tmp_name'])
				  ->setMime($file['type'])
				  ->setTempName($file['name'])
				  ->setSize($file['size'])
				  ->setErrno($file['error']);
			
				if($uploadObj->create()){
					$_POST[$key] = $uploadObj->getUrl();
				}
			}
						
			foreach ($_POST as $name => $value) {
				$this->m->alterValue($name, $value);
			}
			
			Message::setOK('成功修改配置');
		}
		
		$groups = array();
		$optgObj = new \module\system\model\OptionGroupModel();
		$optionGroups = $optgObj->getOptionEnabledGroups();
		foreach($optionGroups as $item) {
			$groups[] = $item['id'];
		}
		
		// 如果配置项组不在列表组中，则不显示列表组
		if(!in_array($group, $groups)) {
			$modObj = new ModuleModel();
			$modObj->setPkv($group);
			if(!$modObj->load()) {
				throw new \core\Exception('该模块未安装，不能设置。');
			}
			
		    $optionGroups = array(
		    	array(
			        'id'    => $group,
			        'name'  => $modObj->name,
			    )
		    );
		}
		
		$options = $this->m->getEditableOptionsByGroup($group);
		$formObj = new \core\util\Form();
		
		foreach ($options as $option) {
			$option['tips'] = htmlspecialchars_decode($option['note']);
			
			// 选项值
			if ($option['values']) {
			    $_vals = trim($option['values']);
				$_vals = explode("\n", $_vals);	
				$option['values'] = array();
			    foreach ($_vals as $_val) {
			    	$_val = trim($_val);
			    	if ($_val === '') {
			    		continue;
			    	}
			    	$option['values'][] = array(
			    	    'name'  => "opt_item_{$option['name']}_{$_val}" == ($_item = Lang::get("opt_item_{$option['name']}_{$_val}")) ? $_val : $_item, 
			    		'value' => $_val
			    	);
				}				
			}			
			$option['rows']  = $option['displayline'];
			$option['tips']  = 'opt_note_'.$option['name'] == ($_tips = Lang::get('opt_note_'.$option['name'])) ? $option['tips'] : $_tips;
			$option['title'] = 'opt_name_'.$option['name'] == ($_title = Lang::get('opt_name_'.$option['name'])) ? $option['title'] : $_title;

			//print_r($option);
			
			$formObj->addElement($option);
		}
		
		$form = $formObj->makeForm(2);
		
		$this->view->assign('optionGroups', $optionGroups);
		$this->view->assign('form', $form);
		$this->view->assign('group', $group);
		$this->view->render();
	}
}