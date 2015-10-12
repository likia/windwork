<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\model;

use core\Config;
/**
 * 权限控制列表模型
 *
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class UIModel {

	/**
	 * 获取所有模板下的主题
	 */
	public static function getStyles() {
		$styles = array();
		$tplsDir = SRC_PATH . 'template/';
		
		$d = dir($tplsDir);
	    while (false !== ($tplId = @$d->read())) {
	        $stylesDir = $tplsDir.$tplId.'/styles/';
	        if($tplId[0] == '.' || !is_dir($tplsDir.$tplId) || !is_dir($stylesDir)) {
	        	continue;
	        }
	        $d2 = dir($stylesDir);
	        while (false !== ($styleId = @$d2->read())) {
	        	$styleDir = $stylesDir.$styleId.'/';
	            if($styleId[0] == '.' || !is_dir($styleDir)) continue;
	        	if (is_file($styleDir.'style.ini')) {
	        		$style = parse_ini_file($styleDir.'style.ini');
	        	} else {
	        		$style = array(
	        			'name'     => $styleId,
        				'author'   => ' - ',
        				'version'  => ' - ',
        				'desc'     => ' - ',
	        		);
	        	}
	        	
	        	$style['tpl']         = $tplId;
	        	$style['id']          = $styleId;
	        	$style['preview']     = is_file($styleDir.'preview.jpg') ? "template/$tplId/styles/$styleId/preview.jpg" : 'static/images/nopic.png';
	        	$style['screenshot']  = is_file($styleDir.'screenshot.jpg') ? "template/$tplId/styles/$styleId/screenshot.jpg" : '';
	        	
	        	
	        	$styles[] = $style;
	        }
	        @$d2->close();
	    }
	    @$d->close();
	    
	    return $styles;
	}
	
	/**
	 * 获取模板列表
	 */
	public function getTemplateList() {
		
	}
	
	/**
	 * 获取系统当前模板路径
	 * @return string
	 */
	public static function getCurrentTplDir() {
		return SRC_PATH . "template/" . Config::get('ui_tpl') . '/';
	}
}
