<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\util;

/**
 * 表单生成类
 * 元素个数据格式
 * 
 * <label><input type="text" name="{$name}" id="{$id}" value="{$value}" /></label>
 * 
 * @todo 未完善
 * @package     core.util
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */
class Form extends \core\Object {
	protected $fields = array();
	protected $elements = array();
	protected $isMakeDiv = false;
	
	protected $hasFormTag = true;
	
	/**
	 * 发送请求到的url,使用自动生成form标签时有效
	 *
	 * @var string
	 */
	protected $action = '';
	
	/**
	 * 打开目标窗口: _blank|_self|_parent|_top|自定义
	 * ,使用自动生成form标签时有效
	 *
	 * @var string
	 */
	protected $target = '_self';
	
	/**
	 * 请求方式 get|post ,使用自动生成form标签时有效
	 *
	 * @var string
	 */
	protected $method = 'post';
	
	/**
	 * 请求表单的id ,使用自动生成form标签时有效
	 *
	 * @var string
	 */
	protected $id = 'wk_form';
	
	/**
	 * 表单编码方式: application/x-www-form-urlencoded | multipart/form-data
	 * 使用自动生成form标签时有效
	 *
	 * @var string
	 */
	protected $enctype = 'multipart/form-data';
	
	/**
	 * 添加一个表单元素
	 * array(
	 *   'type'    => 'hidden|text|text|textarea|checkbox|radio|select|image|custom|date',
	 *   'name'    => '属性名',
	 *   'id'      => '', //可选，不填则 == name，如果有重复则加上下划线和从1开始递增的数字作为后缀
	 *   'values'  => '可选的值', // 对下拉、单选、多选有用
	 *   
	 * );
	 * 
	 * @param array $element
	 */
	public function addElement($element = array()) {
		// 表单元素的type、name属性必须有
		if (empty($element['type']) || empty($element['name'])) {			
			throw new \core\Exception(__CLASS__ . '::addElement() param should have "type" and "name" Element');
		}
		
		// 如果没有指明表单元素的id属性，则使用name属性的值
		$element['id'] = empty($element['id']) ? str_replace(array('[', ']'), '_', $element['name']) : $element['id'];
		
		// 不是隐藏自动必须有title（自定义字段显示的名称）属性
		if (strtolower($element['type']) != 'hidden' && !isset($element['title'])) {
			throw new \core\Exception(__CLASS__ . '::addElement() param should have "title" Element');
		}
		
		// 提供选择的值（单选框，多悬空，下拉菜单有效）
		if (!empty($element['values'])) {
			$element['values'] = self::arrValueFormat($element['values']);			
		}
		
		$this->fields[$element['name']] = $element;
		if (in_array($element['type'], array('text', 'hidden', 'textarea', 'checkbox', 'radio', 'select', 'multiple', 'image', 'custom', 'html', 'date'))) {
			//$toMake = $element['type'];
			$this->{$element['type']}($element);
		}
	}
	
	/**
	 * 添加多个表单元素
	 * 
	 * @param string $elements
	 */
	public function addElements($elements = array()) {
		foreach ($elements as $element) {
			$this->addElement($element);
		}
	}
	
	/**
	 * 单行文本框
	 * 
	 * @param array $element
	 */
	protected function text($element = array()) {
		$html = '<input type="text" name="'.$element['name'].'" id="'.$element['id'].'"
				 class="text ' . (empty($element['class']) ? '' : $element['class']) . '"';
		empty($element['value'])      || $html .= ' value="'. htmlspecialchars($element['value']).'"';
		empty($element['size'])       || $html .= ' size="'.(int)$element['size'].'"';
		empty($element['maxlength'])  || $html .= ' maxlength="'.(int)$element['maxlength'].'"';
		empty($element['append'])     || $html .= ' '.$element['append'];
		$html .= ' />';
		$this->elements[$element['name']] = $html;
		unset($html);
	}
	
	/**
	 * 隐藏域
	 * 
	 * @param array $element
	 */
	protected function hidden($element = array()) {
		$html = "<input type=\"hidden\" name=\"{$element['name']}\" id=\"{$element['id']}\"";
		empty($element['value']) || $html .= ' value="'. htmlspecialchars($element['value']).'"';
		$html .= ' />';
		$this->elements[$element['name']] = $html;
	}
	
	/**
	 * 文件域
	 *
	 * @param array $element
	 */
	protected function image($element = array()) {
		if(empty($element['value'])) {
			$img = 'static/images/nopic.png';
		} else {
			$img = $element['value'];
		}
		
		$imgHtml = '<a href="'.$img.'" target="_blank"><img src="'.$img.'" /></a>';
		
		$html = '<div class="form-image">'.$imgHtml.'</div><input class="file" type="file" size="22" name="'.$element['name'].'" id="'.$element['id'].'"';
		
		empty($element['prepend'])    || $html = $element['prepend'] . $html;
		empty($element['size'])       || $html .= ' size="'. (int)$element['size'].'"';
		empty($element['maxlength'])  || $html .= ' maxlength="'. (int)$element['maxlength'].'"';
		empty($element['append'])     || $html .= ' '.$element['append'];
		$html .= ' />';
		$this->elements[$element['name']] = $html;
	}
	
	/**
	 * 自定义输入框
	 * 
	 * @param array $element
	 */
	protected function custom($element = array()) {
		$this->elements[$element['name']] = @$element['custom'];
	}
	
	/**
	 * 多选框
	 * 
	 * @param array $element
	 */
	protected function checkbox($element = array()) {
		if (empty($element['values'])) {
			throw new \core\Exception(__CLASS__ . '::checkbox() param should have "values" Element');
		}
		
		if(empty($element['value'])) {
			$element['value'] = array();
		} else {
			//$element['value'] = unserialize($element['value']);
			//多选框以选中的值为数组格式
		}
		
		$html = '<div id="'.$element['name'].'" style="display:inline;">';
		empty($element['prepend']) || $html = $element['prepend'] . $html;
		foreach ($element['values'] as $checkboxKey => $checkbox) {
			// $checkbox = array(array('name'=>'', 'value'=>'', 'checked'=>''),...);
			$html .= '<span><input class="checkbox" type="checkbox" name="'.$element['name'].'['.$checkbox['value'].']" id="'.$element['id'].'_'.$checkboxKey.'"';
			
			empty($checkbox['value'])    || $html .= ' value="'. htmlspecialchars($checkbox['value']).'"';
			
			in_array($checkbox['value'], $element['value'])  && $html .= ' checked="checked"';
			$html .= ' /><label for="'.$element['id'].'_'.$checkboxKey.'">' . $checkbox['name'] . ' </label></span>'."\n";			
		}
		$html .='</div>';
		$this->elements[$element['name']] = $html;
	}
	
	/**
	 * 单选框
	 * 
	 * @param array $element
	 */
	protected function radio($element = array()) {
		if (empty($element['values'])) {
			throw new \core\Exception(__CLASS__ . '::radio() param should have "values" Element');
		}
		
		$html = '<div id="'.$element['name'].'" style="display:inline;">';
		foreach ($element['values'] as $radioKey => $radio) {
			// $radio = array(array('name'=>'', 'value'=>'', 'checked'=>''),...);
			$html .= ' <input class="radio" type="radio" name="'.$element['name'].'" id="'.$element['id'].'_'.$radioKey.'"';
			
			empty($radio['name'])    || $html .= ' value="'. htmlspecialchars($radio['value']).'"';
			if(isset($element['value']) && $radio['value'] == $element['value']) {
				$html .= ' checked="checked"';
			}
			$html .= ' /> <label for="'.$element['id'].'_'.$radioKey.'">'. $radio['name'].'</label> &nbsp; '."\n";
		}
		$html .='</div>';
		$this->elements[$element['name']] = $html;
	}
	
	/*
	 * 下拉菜单
	 */
	protected function select($element = array()) {
		if (empty($element['values'])) {
			throw new \core\Exception(__CLASS__ . '::select() param should have "values" Element');
		}
		
		$html = '<select name="'.$element['name'].'" id="'.$element['id'].'"';
		isset($element['multiple'])  && $html .= ' multiple="multiple"'; // 使用可多选
		isset($element['size'])      && $html .= ' size="'.$element['size'].'"';  // 多行择框高度
		empty($element['append'])    || $html .= ' '.$element['append'];
		$html .= '>';
		foreach ($element['values'] as $select) {			
			$html .= '<option value="'. htmlspecialchars($select['value']).'"';
			if ($select['value'] == $element['value']) {
			  	$html .= ' selected';
			}
			$html .= '>'.$select['name'].'</option>';
		}
		$html .='</select>';
		$this->elements[$element['name']] = $html;
	}
	
	/**
	 * 多选菜单
	 * 
	 * @param array $element
	 */
	protected function multiple($element = array()) {
		$element['multiple'] = true;
		$this->select($element);
	}
	/**
	 * 多行文本域
	 * 
	 * @param array $element
	 */
	protected function textarea($element = array()) {		
		empty($element['cols']) && $element['cols'] = 40;
		empty($element['rows']) && $element['rows'] = 8;
		empty($element['class']) && $element['class'] = '';
		
		$html = '<textarea name="'.$element['name'].'" class="'.$element['class'].'" id="'.$element['id'].'"';
		empty($element['cols'])      || $html .= ' cols="'. (int)$element['cols'].'"';
		empty($element['rows'])      || $html .= ' rows="'. (int)$element['rows'].'"';
		isset($element['disabled'])  && $html .= ' disabled';
		isset($element['readonly'])  && $html .= ' readonly="readonly"';
		empty($element['append'])    || $html .= ' '.$element['append'];
		$html .= ' >';		
		empty($element['value'])     || $html .= $element['value'];
		$html .= '</textarea>';
		$this->elements[$element['name']] = $html;
	}
	
	/**
	 * 文本编辑器
	 * 
	 * @param array $element
	 */
	protected function html($element = array()) {
		empty($element['cols']) && $element['cols'] = 68;
		empty($element['rows']) && $element['rows'] = 20;
		empty($element['class']) && $element['class'] = 'editor';
		
		$this->textarea($element);
	}
		
	/**
	 * 日期
	 * 
	 * @param array $element
	 */
	protected function date($element = array()) {
		$html = '<input class="text Wdate" onfocus="WdatePicker()" type="text" name="'.$element['name'].'" id="'.$element['id'].'"';
		empty($element['value'])      || $html .= ' value="'. htmlspecialchars($element['value']).'"';
		empty($element['size'])       || $html .= ' size="'.(int)$element['size'].'"';
		empty($element['maxlength'])  || $html .= ' maxlength="'.(int)$element['maxlength'].'"';
		empty($element['append'])     || $html .= ' '.$element['append'];		
		$html .= ' />';
		$this->elements[$element['name']] = $html;
		unset($html);
	}
	
	/**
	 * 时间
	 * 
	 * @todo
	 * @param array $element
	 */
	protected function time($element = array()) {
		
	}

	/**
	 * 生成表单
	 *
	 * @param array $cells 表单列数 2|3
	 * @param int $cel1Width 表单第1列宽度
	 * @param int $cel2Width 表单第2列宽度，$cells == 3 时有效
	 * @return string
	 */
	public function makeForm($cells = 3) {
		$fnc = $this->isMakeDiv ? "makeDivForm" : "makeTableForm";
		return $this->$fnc($cells);
	}
	
	/**
	 * 生成div表单
	 * @todo
	 * @param int $cells
	 * @param int $cel1Width
	 * @param int $cel2Width
	 */
	public function makeDivForm() {
		$form = '';
		foreach ($this->elements as $key => $element) {
			if ($this->fields[$key]['type'] == 'hidden') {
				continue;
			}
			// 提示信息
			$tips = empty($this->fields[$key]['tips']) ? '&nbsp;' : $this->fields[$key]['tips'];
			$tips = " <span class=\"tips\">{$tips}</span>";
				
			// 必填字段提示符
			if(!empty($this->fields[$key]['required'])) {
				$tips = ' <b><font color="#FF0000;">*</font></b>' . $tips;
			}
				
			// 表单元素结束符后面连接的字符串
			$after = empty($this->fields[$key]['after']) ? '&nbsp;' : $this->fields[$key]['after'];

			$form .= "<div class=\"row row-{$this->fields[$key]['type']}\">\n"
				. ' <h3>'.$this->fields[$key]['title'].'：</h3>'."\n"
				. ' <div>'.$element .$tips. '</div>'."\n"
			    . '</div>'
			    . "<div class=\"clear\"></div>\n";
			
		}
		
		return $form;
	}
	
	public function makeByGroup() {
		$rs = array();
		foreach ($this->elements as $key => $element) {
			if ($this->fields[$key]['type'] == 'hidden') {
				continue;
			}
			
			$field = $this->fields[$key];
			if (!isset($rs[$field['groupid']])) {
				$rs[$field['groupid']] = array();
			}
			
			// 提示信息
			$tips = empty($field['tips']) ? '&nbsp;' : $field['tips'];
			$tips = " <span class=\"tips\">{$tips}</span>";
				
			// 必填字段提示符
			if(!empty($field['required'])) {
				$tips = ' <b><font color="#FF0000;">*</font></b>' . $tips;
			}
				
			// 表单元素结束符后面连接的字符串
			$after = empty($field['after']) ? '&nbsp;' : $field['after'];
			
			$rs[$field['groupid']][] = "<div class=\"row row-{$field['type']}\">\n"
				. ' <h3>'.$this->fields[$key]['title'].'：</h3>'."\n"
				. ' <div>'.$element .$tips. '</div>'."\n"
			    . '</div>'
			    . "<div class=\"clear\"></div>\n";
			
		}
		
		return $rs;		
	}

	/**
	 * 生成表单
	 * 
	 * @param array $cells 表单表格的列数 2|3
	 * @param int $cel1Width 表单第1列宽度
	 * @param int $cel2Width 表单第2列宽度，$cells == 3 时有效
	 * @return string
	 */
	protected function makeTableForm($cells = 3) {
		$form = '';
		$this->hasFormTag &&
		$form = '<form '
		       .'method="'   .$this->method    .'" '
		       .'enctype="'  .$this->enctype   .'" '
		       .'action="'   .$this->action    .'" '
		       .'target="'   .$this->target    .'" '
		       .'id="'       .$this->id        .'" '
		                     .$this->append    .'>'."\n";
		// 先生成隐藏自动
		foreach ($this->elements as $key => $element) {
			if ($this->fields[$key]['type'] == 'hidden') {
				$form .= "$element\n";
				continue;
			}
		}
		$form .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form tb">'."\n";
		foreach ($this->elements as $key => $element) {
			if ($this->fields[$key]['type'] == 'hidden') {
				continue;
			}
			// 提示信息
			$tips = empty($this->fields[$key]['tips']) ? '&nbsp;' : $this->fields[$key]['tips'];
			$tips = " <span class=\"tips\">{$tips}</span>";
			
			// 必填字段提示符
			if(!empty($this->fields[$key]['required'])) {
				$tips = ' <b><font color="#FF0000;">*</font></b>' . $tips;
			}
			
			// 表单元素结束符后面连接的字符串
			$after = empty($this->fields[$key]['after']) ? '&nbsp;' : $this->fields[$key]['after'];
			
			if($cells == 2) {
				$form .= '  <tr>'."\n"
			            .'    <th class="form-left" align="right" valign="middle"><label for="'.$key.'">'.$this->fields[$key]['title'].'：</label></th>'."\n"
			            .'    <td class="form-right" align="left" valign="middle">'.$element .$tips. '</td>'."\n"
			            .'  </tr>'."\n";
			} else {
			    $form .= '  <tr>'."\n"
			            .'    <th class="form-left" align="right" valign="middle">'.$this->fields[$key]['title'].'：</th>'."\n"
			            .'    <td class="form-center" align="left" valign="middle">'.$element . '</td>'."\n"
			            .'    <td class="form-right" align="left" valign="middle">'.$tips.'</td>'."\n"
			            .'  </tr>'."\n";
			}
		}
		$this->hasFormTag && 
            $form  .= '  <tr>'."\n"
                     .'    <th class="form-left">&nbsp;</th>'
                     .'    <td class="form-center">'
                     .'      <input type="submit" id="submit_btn" value="提交" class="btn">'
                     .'      <input type="reset" id="reset_btn" value="重置" class="btn">'
                     .'    </td>'
                     .'    <td class="form-right">&nbsp;</td>'
                     .'  </tr>'."\n";
		$form .= '</table>'."\n";
		
	    $this->hasFormTag && $form .= '</form>'."\n";
	    
	    return $form;
	}

	/**
	 * 设置form标签中action属性的值，使用自动生成form标签时有效
	 * 
	 * @param string $action
	 * @return \core\util\Form
	 */
	public function setAction($action = '') {
		$this->action = $action;
		return $this;
	}
	
	/**
	 * 设置表单的enctype属性的值，使用自动生成form标签时有效
	 * 
	 * @param string $enctype
	 * @return \core\util\Form
	 */
	public function setEnctype($enctype = 'application/x-www-form-urlencoded') {
		$this->enctype = $enctype;
		return $this;
	}
	
	/**
	 * 设置表单的id属性的值，使用自动生成form标签时有效
	 *
	 * @param string $id
	 * @return \core\util\Form
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * 设置表单的method属性的值，使用自动生成form标签时有效
	 * 
	 * @param string $method
	 * @return \core\util\Form
	 */
	public function setMethod($method = 'post') {
		$this->method = $method;
		return $this;
	}
	
	/**
	 * 设置表单的target属性的值，使用自动生成form标签时有效
	 * 
	 * @param string $target
	 * @return \core\util\Form
	 */
	public function setTarget($target = '_self') {
		$this->target = $target;
		return $this;
	}
	
	/**
	 * 设置<form ... 这里添加的内容> 
	 *
	 * @param string $append
	 * @return \core\util\Form
	 */
	public function setAppend($append = '') {
		$this->append = $append;
		return $this;
	}
	
	/**
	 * 设置是否生成form标签
	 *
	 * @param bool $has
	 * @return \core\util\Form
	 */
	public function setHasFormTag($has = true) {
		$this->hasFormTag = $has;
		return $this;
	}
	
	/**
	 * 格式化表单可选选项值
	 * 将以换行隔开的字符串转换成每行一个选项的数组格式，
	 * 
	 * @param string|array $values
	 * @return array
	 */
	public static function arrValueFormat($values) {
		if (is_array($values)) {
			return $values;
		}	        
			
		$values = trim($values);
		$values = explode("\n", $values);
		$valuesArr = array();
	    foreach ($values as $value) {
			$_temp = explode("=", $value);
			isset($_temp[1]) || $_temp[1] = $_temp[0];
			$valuesArr[trim($_temp[0])] = array('name' => trim($_temp[1]), 'value' => trim($_temp[0]));
		}
		
		return $valuesArr;
	}
	
	/**
	 * 设置是否生成div描述的表单，false时使用table表现方式
	 * @param bool $isMakeDiv
	 * @return \core\util\Form
	 */
	public function setIsMakeDiv($isMakeDiv) {
		$this->isMakeDiv = (bool)$isMakeDiv;
		return $this;
	}
}
