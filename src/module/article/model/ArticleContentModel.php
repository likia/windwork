<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\article\model;


class ArticleContentModel extends \core\mvc\Model {
	protected $table = 'wk_article_content';
	public function create() {
		return $this->replace();
	}
	
	public function update(){
		return $this->replace();;
	}
	
	public function replace() {
		if (!$this->getPkv()) {
			$this->setErr('错误：请设置文章id！');
			return false;
		}
		
		if (!$this->content) {
			$this->setErr('错误：请设置文章内容！');
			return false;
		}
		
		return parent::replace();
	}
}